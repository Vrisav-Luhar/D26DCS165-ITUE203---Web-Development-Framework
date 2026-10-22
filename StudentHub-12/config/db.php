<?php
/*
 * StudentHub - Practical 8
 * Reusable PDO connection.
 *
 * Default XAMPP configuration often uses root with an empty password.
 * Update these values if your local MySQL configuration differs.
 */

$host = 'localhost';
$dbname = 'studenthub_db';
$username = 'root';
$password = '';

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    error_log('StudentHub database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed. Check that MySQL is running, studenthub_db has been imported, and config/db.php credentials are correct.');
}
