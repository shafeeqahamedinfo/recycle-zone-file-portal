<?php
// db.php - edit with your DB credentials
$DB_HOST = 'localhost';
$DB_NAME = 'file_portal';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$baseUpload = __DIR__ . '/uploads';
$publicDir = $baseUpload . '/public';
$privateDir = $baseUpload . '/private';

if (!is_dir($baseUpload)) mkdir($baseUpload, 0755);
if (!is_dir($publicDir)) mkdir($publicDir, 0755);
if (!is_dir($privateDir)) mkdir($privateDir, 0755);
