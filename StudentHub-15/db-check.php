<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';
studenthub_require_role($pdo, 'admin');

$database = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
$studentCount = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$eventCount = (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Practical 13 Database Check</title><link rel="stylesheet" href="css/style.css">
<style>.wrap{max-width:850px;margin:2rem auto;padding:1rem}.card{background:#fff;border:1px solid #ddd;border-radius:12px;padding:1.5rem;margin:1rem 0}code{background:#f2f2f2;padding:.15rem .35rem;border-radius:4px}</style></head><body>
<header class="site-header"><div class="container nav-container"><a href="index.html" class="logo">StudentHub</a><nav class="main-nav"><a href="admin-dashboard.php">Dashboard</a><a href="api-demo.php">Fetch API Demo</a><a href="logout.php">Logout</a></nav></div></header>
<main class="wrap"><h1>Practical 13 Database Check</h1><div class="card">
<p><strong>Database used by this project:</strong> <code><?=htmlspecialchars($database, ENT_QUOTES, 'UTF-8')?></code></p>
<p><strong>Students currently stored:</strong> <?= $studentCount ?></p>
<p><strong>Events currently stored:</strong> <?= $eventCount ?></p>
<p>This page reads counts through the same PDO connection used by the APIs. Compare these results with phpMyAdmin, ensuring the same database is selected.</p>
<p><a href="api-demo.php">Return to Fetch API Demo</a></p>
</div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer></body></html>
