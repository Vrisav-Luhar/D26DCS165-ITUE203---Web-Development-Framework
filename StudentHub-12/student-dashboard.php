<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';
studenthub_require_role($pdo, 'student');

$stmt = $pdo->prepare('SELECT student_id, full_name, email, mobile, course, study_year, created_at FROM students WHERE student_id = :id LIMIT 1');
$stmt->execute(['id' => (int)$_SESSION['student_id']]);
$student = $stmt->fetch();
if (!$student) {
    header('Location: logout.php');
    exit;
}
function e_student(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard - StudentHub</title><link rel="stylesheet" href="css/style.css">
<style>.dash-wrap{max-width:950px;margin:2rem auto;padding:0 1rem}.dash-card{border:1px solid #ddd;border-radius:14px;padding:1.5rem;margin:1rem 0;background:var(--card-bg,#fff)}.dash-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem}.dash-label{opacity:.7;font-size:.9rem}.dash-value{font-weight:600;overflow-wrap:anywhere}.top-actions{display:flex;gap:1rem;flex-wrap:wrap;align-items:center;justify-content:space-between}</style>
</head><body>
<header class="site-header"><div class="container nav-container"><a href="index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="events.html">Events</a><a href="profile.html">Profile</a><a href="logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Student Dashboard</h1><p>Welcome, <?= e_student((string)$student['full_name']) ?>.</p></div></section>
<div class="dash-wrap"><div class="dash-card"><div class="top-actions"><div><h2>My Account</h2><p>You are signed in as a student.</p></div><a class="btn btn-primary" href="logout.php">Logout securely</a></div>
<div class="dash-grid">
<div><p class="dash-label">Student ID</p><p class="dash-value"><?= (int)$student['student_id'] ?></p></div>
<div><p class="dash-label">Full Name</p><p class="dash-value"><?= e_student((string)$student['full_name']) ?></p></div>
<div><p class="dash-label">Email</p><p class="dash-value"><?= e_student((string)$student['email']) ?></p></div>
<div><p class="dash-label">Mobile</p><p class="dash-value"><?= e_student((string)($student['mobile'] ?? 'Not provided')) ?></p></div>
<div><p class="dash-label">Course</p><p class="dash-value"><?= e_student((string)$student['course']) ?></p></div>
<div><p class="dash-label">Study Year</p><p class="dash-value"><?= (int)$student['study_year'] ?></p></div>
</div></div>
<p><a href="index.html">Back to StudentHub home</a></p></div></main>
<footer class="site-footer"><div class="container"><p>&copy; <?= date('Y') ?> StudentHub</p></div></footer><script src="js/script.js"></script></body></html>
