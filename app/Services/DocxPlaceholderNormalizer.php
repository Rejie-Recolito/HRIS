<?php
namespace App\Services;

class DocxPlaceholderNormalizer
{
    /**
     * Normalize the docx template by collapsing any XML tags inside placeholder tokens ${...}
     * Writes a normalized copy to $outPath and returns the outPath.
     *
     * @param string $srcPath
     * @param string $outPath
     * @return string
     */
    public static function normalizeTemplate(string $srcPath, string $outPath): string
    {
        if (!file_exists($srcPath)) {
            throw new \RuntimeException("Source template not found: {$srcPath}");
        }

        $zip = new \ZipArchive();
        if ($zip->open($srcPath) !== true) {
            throw new \RuntimeException("Failed to open docx: {$srcPath}");
        }

        $docXml = $zip->getFromName('word/document.xml');
        if ($docXml === false) {
            $zip->close();
            throw new \RuntimeException('document.xml not found in docx');
        }

        // Collapse any XML tags inside ${...} tokens so placeholders become continuous text
        $offset = 0;
        while (false !== ($pos = strpos($docXml, '${', $offset))) {
            $end = strpos($docXml, '}', $pos);
            if ($end === false) break;
            $substr = substr($docXml, $pos, $end - $pos + 1);
            // Remove XML tags inside the placeholder
            $clean = preg_replace('/<[^>]+>/', '', $substr);
            // Also normalize whitespace inside
            $clean = preg_replace('/\s+/', ' ', $clean);
            $docXml = substr_replace($docXml, $clean, $pos, $end - $pos + 1);
            $offset = $pos + strlen($clean);
        }

        // Create new zip and write files, replacing document.xml
        $newZip = new \ZipArchive();
        if ($newZip->open($outPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $zip->close();
            throw new \RuntimeException("Failed to create normalized docx: {$outPath}");
        }
        // Copy all entries, but replace document.xml
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $name = $stat['name'];
            if ($name === 'word/document.xml') {
                $newZip->addFromString($name, $docXml);
                continue;
            }
            $contents = $zip->getFromName($name);
            if ($contents === false) continue;
            $newZip->addFromString($name, $contents);
        }

        $zip->close();
        $newZip->close();
        return $outPath;
    }
}
