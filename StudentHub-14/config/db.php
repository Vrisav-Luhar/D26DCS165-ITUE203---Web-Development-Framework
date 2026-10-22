<?php
declare(strict_types=1);

/**
 * StudentHub Practical 13 PDO connection.
 * Keep this database name consistent with phpMyAdmin.
 */
$host = 'localhost';
$dbname = 'studenthub_db';
$username = 'root';
$password = '';

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $exception) {
    error_log('StudentHub database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed. Check MySQL, database name, and config/db.php credentials.');
}
