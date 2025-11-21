<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$phpWord = new PhpWord();
$section = $phpWord->addSection();

$section->addText('SERVICE RECORD TEMPLATE', ['bold' => true]);
$section->addText('NAME: ${lastname} ${firstname} ${middlename}');
$section->addText('BIRTH: ${birth}');
$section->addText('PLACE OF BIRTH: ${place_of_birth}');
$section->addText('');
// Date accomplished placeholder
$section->addText('DATE ACCOMPLISHED: ${date_accomplished}');

// Add a table header that matches the placeholders used in code
$table = $section->addTable();
$table->addRow();
$headers = ['From', 'To', 'Rank', 'Designation', 'Status', 'Monthly Pay', 'Station', 'Branch', 'Leave of Absence', 'Sep Date', 'Sep Cause'];
foreach ($headers as $h) {
    $table->addCell(1000)->addText($h);
}

// Add a row with placeholders (for TemplateProcessor cloneRow example use 'from' as main col)
$table->addRow();
$table->addCell(1000)->addText('${from}');
$table->addCell(1000)->addText('${to}');
$table->addCell(1000)->addText('${rank}');
$table->addCell(1000)->addText('${designation}');
$table->addCell(1000)->addText('${status}');
$table->addCell(1000)->addText('${monthly_pay}');
$table->addCell(1000)->addText('${station}');
$table->addCell(1000)->addText('${branch}');
$table->addCell(1000)->addText('${leave_of_absence}');
$table->addCell(1000)->addText('${sep_date}');
$table->addCell(1000)->addText('${sep_cause}');

$out = __DIR__ . '/../resources/templates/service_record_template.docx';
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save($out);

echo "Wrote template to: $out\n";
