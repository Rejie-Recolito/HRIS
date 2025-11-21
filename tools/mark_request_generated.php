<?php
// Usage: php mark_request_generated.php <request_id> <storage_relative_path>
if ($argc < 3) {
    echo "Usage: php mark_request_generated.php <request_id> <storage_relative_path>\n";
    exit(1);
}
$reqId = (int)$argv[1];
$path = $argv[2];
$dbFile = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$now = (new DateTime())->format('Y-m-d H:i:s');
$stmt = $pdo->prepare('UPDATE service_record_requests SET request_status = ?, generated_pdf_path = ?, generated_at = ? WHERE id = ?');
$res = $stmt->execute(['generated', $path, $now, $reqId]);
if ($res) {
    echo "Updated request $reqId -> generated, path=$path, generated_at=$now\n";
} else {
    $err = $pdo->errorInfo();
    echo "Failed: " . json_encode($err) . "\n";
}
