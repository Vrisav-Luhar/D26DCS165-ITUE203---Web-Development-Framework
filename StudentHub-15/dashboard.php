<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';

// auth.php and this check ensure that only signed-in accounts reach this page.
if (empty($_SESSION['student_id']) || empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}
$role = (string)$_SESSION['role'];
if (!in_array($role, ['admin', 'student'], true)) {
    http_response_code(403);
    exit('Access denied.');
}
require_once __DIR__ . '/includes/dynamic_menu.php';

function e_dashboard(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$isAdmin = $role === 'admin';
$name = (string)($_SESSION['full_name'] ?? $_SESSION['name'] ?? 'User');
$studentCount = 0;
$eventCount = 0;
$upcomingEvents = [];
$profile = null;

try {
    if ($isAdmin) {
        $studentCount = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
        $eventCount = (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
        $upcomingEvents = $pdo->query('SELECT event_id,title,event_date,venue FROM events ORDER BY event_date ASC LIMIT 5')->fetchAll();
    } else {
        $stmt = $pdo->prepare('SELECT student_id,full_name,email,course,study_year,mobile FROM students WHERE student_id=:id LIMIT 1');
        $stmt->execute(['id' => (int)$_SESSION['student_id']]);
        $profile = $stmt->fetch() ?: null;
        $upcomingEvents = $pdo->query('SELECT event_id,title,event_date,venue FROM events ORDER BY event_date ASC LIMIT 5')->fetchAll();
    }
} catch (PDOException $e) {
    error_log('StudentHub Practical 14 dashboard: ' . $e->getMessage());
    $dashboardError = 'Some dashboard information could not be loaded. Check your database connection.';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $isAdmin ? 'Admin Dashboard' : 'Student Dashboard' ?> - StudentHub</title>
<link rel="stylesheet" href="css/style.css">
<style>
.dash-wrap{max-width:1150px;margin:2rem auto;padding:0 1rem}.dash-card{background:var(--card-bg,#fff);border:1px solid #ddd;border-radius:12px;padding:1.4rem;margin:1rem 0}
.dash-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:1rem}.stat{border:1px solid #ddd;border-radius:10px;padding:1rem}.stat strong{font-size:1.8rem;display:block;margin-top:.5rem}
.quick-links{display:flex;gap:.7rem;flex-wrap:wrap}.quick-links a{display:inline-block;border:1px solid #bbb;border-radius:8px;padding:.7rem 1rem;text-decoration:none}
table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:.7rem;border-bottom:1px solid #ddd}.table-wrap{overflow-x:auto}
.main-nav a[aria-current="page"]{font-weight:bold;text-decoration:underline}.notice{padding:.8rem;background:#fff4d6;border-radius:8px}
</style>
</head>
<body>
<header class="site-header"><div class="container nav-container">
<a href="index.html" class="logo">Student<span>Hub</span></a>
<?php studenthub_render_menu('dashboard'); ?>
</div></header>
<main>
<section class="page-header"><div class="container">
<h1><?= $isAdmin ? 'Admin Dashboard' : 'Student Dashboard' ?></h1>
<p>Welcome, <?= e_dashboard($name) ?>. <?= $isAdmin ? 'Administrator access is active.' : 'Your student portal is ready.' ?></p>
</div></section>
<div class="dash-wrap">
<?php if (!empty($dashboardError)): ?><p class="notice" role="status"><?= e_dashboard($dashboardError) ?></p><?php endif; ?>

<?php if ($isAdmin): ?>
<section class="dash-card"><h2>Portal Overview</h2>
<div class="dash-grid">
<div class="stat">Total registered users<strong><?= $studentCount ?></strong></div>
<div class="stat">Total events<strong><?= $eventCount ?></strong></div>
</div></section>
<section class="dash-card"><h2>Administration</h2><p>Menu links are displayed according to your role. Admins can manage student and event records.</p>
<div class="quick-links">
<a href="admin/students.php">Manage Students</a><a href="admin/events.php">Manage Events</a><a href="api-demo.php">AJAX / Fetch CRUD</a><a href="db-check.php">Database Check</a>
</div></section>
<?php else: ?>
<section class="dash-card"><h2>My Account</h2>
<?php if ($profile): ?><div class="dash-grid">
<div class="stat">Student ID<strong><?= (int)$profile['student_id'] ?></strong></div>
<div class="stat">Full Name<strong><?= e_dashboard((string)$profile['full_name']) ?></strong></div>
<div class="stat">Email<strong><?= e_dashboard((string)$profile['email']) ?></strong></div>
<div class="stat">Course<strong><?= e_dashboard((string)$profile['course']) ?></strong></div>
<div class="stat">Study Year<strong><?= (int)$profile['study_year'] ?></strong></div>
</div><?php else: ?><p>Profile details are not available.</p><?php endif; ?>
</section>
<section class="dash-card"><h2>Quick Links</h2><div class="quick-links"><a href="admin/events.php">Browse Events</a><a href="profile.php">My Profile</a></div></section>
<?php endif; ?>

<section class="dash-card"><h2><?= $isAdmin ? 'Recent Events' : 'Upcoming Events' ?></h2>
<?php if (!$upcomingEvents): ?><p>No events available.</p><?php else: ?>
<div class="table-wrap"><table><thead><tr><th>Event</th><th>Date and Time</th><th>Venue</th></tr></thead><tbody>
<?php foreach ($upcomingEvents as $event): ?><tr><td><?= e_dashboard((string)$event['title']) ?></td><td><?= e_dashboard((string)$event['event_date']) ?></td><td><?= e_dashboard((string)$event['venue']) ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?>
</section>
</div>
</main>
<footer class="site-footer"><div class="container"><p>&copy; <?= date('Y') ?> StudentHub</p></div></footer>
<script src="js/script.js"></script>
</body></html>
