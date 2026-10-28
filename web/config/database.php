<?php
// web/config/database.php

// Load .env file manually (for local development without server-level env vars)
$_envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($_envFile)) {
    $lines = file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$_k, $_v] = explode('=', $line, 2);
            $_k = trim($_k);
            $_v = trim($_v);
            if (!getenv($_k)) {
                putenv("$_k=$_v");
                $_ENV[$_k] = $_v;
            }
        }
    }
    unset($lines, $line, $_k, $_v);
}
unset($_envFile);

$host    = getenv('DB_HOST')     ?: 'localhost';
$db      = getenv('DB_NAME')     ?: 'dental_clinic';
$user    = getenv('DB_USER')     ?: 'dental_user';
$pass    = getenv('DB_PASSWORD') ?: 'dental_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = null;
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET NAMES utf8mb4");
} catch (\PDOException $e) {
    // Soft error handling: set $pdo = null so API/Chatbot can still function without database
    $pdo = null;
}

/**
 * Get PDO connection instance
 * @return PDO|null
 */
function getDB() {
    global $pdo;
    return $pdo;
}
