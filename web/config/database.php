<?php
// web/config/database.php

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'dental_clinic';
$user = getenv('DB_USER') ?: 'dental_user';
$pass = getenv('DB_PASSWORD') ?: 'dental_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET NAMES utf8mb4");
} catch (\PDOException $e) {
    // Return friendly error if database is not reachable yet
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}

/**
 * Get PDO connection instance
 * @return PDO
 */
function getDB() {
    global $pdo;
    return $pdo;
}
