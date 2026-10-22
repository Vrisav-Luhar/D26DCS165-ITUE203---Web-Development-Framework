<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';
studenthub_require_role($pdo, 'admin');

$count = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$events = (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
function e_admin(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - StudentHub</title><link rel="stylesheet" href="css/style.css">
<style>.dash-wrap{max-width:950px;margin:2rem auto;padding:0 1rem}.dash-card{border:1px solid #ddd;border-radius:14px;padding:1.5rem;margin:1rem 0;background:var(--card-bg,#fff)}.dash-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem}.metric{font-size:2rem;font-weight:700}.top-actions{display:flex;gap:1rem;flex-wrap:wrap;align-items:center;justify-content:space-between}</style>
</head><body>
<header class="site-header"><div class="container nav-container"><a href="index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="index.html">Home</a><a href="logout.php">Logout</a></nav></div></header>
<main><p style="text-align:center"><a href="dashboard.php">Open Practical 14 Dynamic Dashboard</a></p><section class="page-header"><div class="container"><h1>Admin Dashboard</h1><p>Welcome, <?= e_admin((string)$_SESSION['student_name']) ?>. Administrator access is active.</p></div></section>
<div class="dash-wrap"><div class="dash-card"><div class="top-actions"><h2>Portal Overview</h2><a class="btn btn-primary" href="logout.php">Logout securely</a></div>
<div class="dash-grid"><div><p>Total registered users</p><p class="metric"><?= $count ?></p></div><div><p>Total events</p><p class="metric"><?= $events ?></p></div></div>
<p>This Practical 10 page demonstrates role-protected admin access.</p><p><a class="btn btn-primary" href="admin/students.php">Manage Students (Practical 11)</a> <a class="btn btn-primary" href="admin/events.php">Manage Events (Practical 12)</a></p><p><a class="btn btn-primary" href="api-demo.php">AJAX / Fetch CRUD (Practical 13)</a></p></div></div></main>
<footer class="site-footer"><div class="container"><p>&copy; <?= date('Y') ?> StudentHub</p></div></footer><script src="js/script.js"></script></body></html>
