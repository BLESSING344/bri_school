<?php
// Determine environment parameters
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_DATABASE') ?: 'bri_school';
$db_user = getenv('DB_USERNAME') ?: 'root';
$db_pass = getenv('DB_PASSWORD') ?: '';
$db_connection = getenv('DB_CONNECTION') ?: 'mysql';

// Render DATABASE_URL support (PostgreSQL)
$DATABASE_URL = getenv('DATABASE_URL');

try {
    if ($DATABASE_URL) {
        // Automatically parse Render's PostgreSQL URL
        $dbopts = parse_url($DATABASE_URL);
        $pdo = new PDO(
            "pgsql:host=" . $dbopts['host'] . ";port=" . ($dbopts['port'] ?? 5432) . ";dbname=" . ltrim($dbopts['path'], '/'),
            $dbopts['user'],
            $dbopts['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    } else {
        // Fallback to MySQL for Local Development
        $pdo = new PDO(
            "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4",
            $db_user,
            $db_pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>