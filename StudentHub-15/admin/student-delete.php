<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/audit.php';
studenthub_require_role($pdo, 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students.php');
    exit;
}
$csrf = (string)($_POST['csrf_token'] ?? '');
if (empty($_SESSION['students_csrf']) || !hash_equals($_SESSION['students_csrf'], $csrf)) {
    http_response_code(403);
    exit('Invalid form token. Return to Manage Students and try again.');
}
$id = filter_var($_POST['student_id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid student ID.');
}
if ($id === (int)$_SESSION['student_id']) {
    header('Location: students.php?roleblocked=1');
    exit;
}
try {
    $before = $pdo->prepare('SELECT full_name, email FROM students WHERE student_id = :id LIMIT 1');
    $before->execute(['id' => $id]);
    $oldStudent = $before->fetch();
    $stmt = $pdo->prepare('DELETE FROM students WHERE student_id = :id');
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() > 0) {
        studenthub_audit($pdo, 'DELETE', 'student', (int)$id, 'Deleted student account: ' . (string)($oldStudent['email'] ?? 'unknown'));
    }
    header('Location: students.php?deleted=1');
    exit;
} catch (PDOException $ex) {
    error_log('StudentHub delete student: ' . $ex->getMessage());
    http_response_code(500);
    exit('Could not delete this record. Check whether related records or database constraints prevent deletion.');
}
