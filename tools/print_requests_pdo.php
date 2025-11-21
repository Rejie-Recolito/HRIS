<?php
$dbFile = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbFile)) { echo "DB not found: $dbFile\n"; exit(1); }
$pdo = new PDO('sqlite:' . $dbFile);
$stmt = $pdo->query("SELECT id, user_id, name, request_status, generated_pdf_path, generated_at, created_at FROM service_record_requests ORDER BY datetime(created_at) DESC LIMIT 50");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
printf("%6s %8s %12s %40s %20s %s\n","id","user_id","status","generated_pdf_path","generated_at","name");
foreach ($rows as $row) {
    printf("%6s %8s %12s %40s %20s %s\n",
        $row['id'], $row['user_id'], $row['request_status'], substr($row['generated_pdf_path'] ?? '',0,38), $row['generated_at'] ?? '', $row['name']
    );
}
