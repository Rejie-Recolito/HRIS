<?php
// Fix missing phone/email values and remove any leftover SQLite temp table
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "Database file not found: {$dbPath}\n";
    exit(1);
}
$db = new SQLite3($dbPath);
// Update NULLs to empty string
$db->exec("UPDATE employee SET phone_number = '' WHERE phone_number IS NULL;");
$db->exec("UPDATE employee SET email_address = '' WHERE email_address IS NULL;");
// Drop leftover temp table if present
$db->exec('DROP TABLE IF EXISTS "__temp__employee"');
echo "OK\n";
