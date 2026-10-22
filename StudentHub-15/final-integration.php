<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';

if (empty($_SESSION['student_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}
$role = (string)$_SESSION['role'];
if (!in_array($role, ['admin', 'student'], true)) {
    http_response_code(403);
    exit('Access denied.');
}
$isAdmin = $role === 'admin';
function e_final(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
$name = (string)($_SESSION['full_name'] ?? $_SESSION['name'] ?? 'User');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>StudentHub Final Integration</title>
<link rel="stylesheet" href="css/style.css">
<style>
.final-wrap{max-width:1100px;margin:2rem auto;padding:0 1rem}.final-card{background:var(--card-bg,#fff);border:1px solid #ddd;border-radius:12px;padding:1.4rem;margin:1rem 0}
.module-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem}.module{border:1px solid #ddd;border-radius:10px;padding:1rem}.module h3{margin-top:0}.module a{display:inline-block;margin-top:.4rem}
.status-list{padding-left:1.3rem}.status-list li{margin:.45rem 0}.pill{display:inline-block;padding:.25rem .6rem;border:1px solid #bbb;border-radius:999px;font-size:.9rem}
</style>
</head>
<body>
<header class="site-header"><div class="container nav-container">
<a href="index.html" class="logo">Student<span>Hub</span></a>
<nav class="main-nav" aria-label="Main navigation">
<a href="final-integration.php" aria-current="page">Final Integration</a>
<a href="dashboard.php">Dashboard</a>
<?php if ($isAdmin): ?>
<a href="admin/students.php">Manage Students</a>
<a href="admin/events.php">Manage Events</a>
<a href="api-demo.php">Fetch API Demo</a>
<?php else: ?>
<a href="profile.php">Profile</a>
<?php endif; ?>
<a href="logout.php">Logout</a>
</nav>
</div></header>
<main>
<section class="page-header"><div class="container">
<h1>StudentHub — Final Integration</h1>
<p>Welcome, <?=e_final($name)?>. Signed in as <span class="pill"><?=e_final($role)?></span></p>
</div></section>
<div class="final-wrap">
<section class="final-card">
<h2>Project Modules</h2>
<p>This page brings together the modules developed during the StudentHub practicals. Only open the modules permitted for your account.</p>
<div class="module-grid">
<?php if ($isAdmin): ?>
<div class="module"><h3>Student Management</h3><p>Create, view, search, edit and delete student records.</p><a href="admin/students.php">Open student CRUD</a></div>
<div class="module"><h3>Event Management</h3><p>Manage event details and inspect registration counts.</p><a href="admin/events.php">Open event CRUD</a></div>
<div class="module"><h3>AJAX / Fetch API</h3><p>Test JSON API operations without a full page reload.</p><a href="api-demo.php">Open Fetch demo</a></div>
<div class="module"><h3>Database Check</h3><p>Check the active database and record counts.</p><a href="db-check.php">Open database check</a></div>
<?php else: ?>
<div class="module"><h3>Student Dashboard</h3><p>View your account dashboard.</p><a href="student-dashboard.php">Open dashboard</a></div>
<div class="module"><h3>My Profile</h3><p>View your student account details.</p><a href="profile.php">Open profile</a></div>
<div class="module"><h3>Events</h3><p>View the event list.</p><a href="admin/events.php">Open events</a></div>
<?php endif; ?>
</div>
</section>
<section class="final-card">
<h2>Final verification checklist</h2>
<p>Complete these checks manually in your local XAMPP setup and record the result in <code>docs/TEST-REPORT.md</code>.</p>
<ul class="status-list">
<li>Login and logout work; invalid login is rejected.</li>
<li>Admin and Student see role-appropriate menus and pages.</li>
<li>Student CRUD works and rejects invalid inputs.</li>
<li>Event CRUD works; poster/file validation is tested if present in your version.</li>
<li>Fetch API lists and changes records; errors display clearly.</li>
<li>Database constraints and foreign keys behave as expected.</li>
<li>Pages remain usable on desktop and mobile-sized windows.</li>
<li>Forms have labels, keyboard navigation, and visible validation feedback.</li>
</ul>
</section>
<section class="final-card">
<h2>Submission files</h2>
<ul>
<li><code>README.md</code> — setup and usage guide</li>
<li><code>docs/TEST-REPORT.md</code> — test results</li>
<li><code>docs/SECURITY-ACCESSIBILITY-CHECKLIST.md</code> — review checklist</li>
<li><code>database/</code> — save your SQL export from phpMyAdmin here before submission</li>
</ul>
<p><strong>Reminder:</strong> this page is an integration landing page, not proof that every test passed. Complete the checks in your own environment.</p>
</section>
</div>
</main>
<footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer>
<script src="js/script.js"></script>
</body>
</html>
