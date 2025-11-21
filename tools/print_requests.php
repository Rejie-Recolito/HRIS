<?php
$db = new SQLite3(__DIR__ . '/../database/database.sqlite');
$results = $db->query("SELECT id, user_id, name, request_status, generated_pdf_path, generated_at, created_at FROM service_record_requests ORDER BY datetime(created_at) DESC LIMIT 50");
echo str_pad('id',6) . str_pad('user_id',10) . str_pad('status',15) . str_pad('generated_pdf_path',40) . str_pad('generated_at',25) . "name\n";
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    printf("%6s%10s%15s%40s%25s %s\n",
        $row['id'], $row['user_id'], $row['request_status'], substr($row['generated_pdf_path'] ?? '',0,38), $row['generated_at'] ?? '', $row['name']
    );
}
