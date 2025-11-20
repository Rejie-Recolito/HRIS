<?php
$template = __DIR__ . '/../resources/templates/Service-Record-template.docx';
$f = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test-srvrec.docx';
if (file_exists($template)) {
    copy($template, $f);
    echo "Copied template to $f\n";
} else {
    file_put_contents($f, "Test content\n");
    echo "Created dummy file $f\n";
}
$outdir = sys_get_temp_dir();
$soffice = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe"';
$cmd = $soffice . " --headless --convert-to pdf --outdir " . escapeshellarg($outdir) . " " . escapeshellarg($f);
echo "Running: $cmd\n";
passthru($cmd, $r);
echo "Exit code: $r\n";
$pdf = $outdir . DIRECTORY_SEPARATOR . pathinfo($f, PATHINFO_FILENAME) . '.pdf';
echo "PDF exists? " . (file_exists($pdf) ? 'yes' : 'no') . "\n";
