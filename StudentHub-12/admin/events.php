<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
studenthub_require_role($pdo, 'admin');

if (empty($_SESSION['events_csrf'])) {
    $_SESSION['events_csrf'] = bin2hex(random_bytes(32));
}
function e_events(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$search = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];
if ($search !== '') {
    $where = ' WHERE title LIKE :q_title OR venue LIKE :q_venue OR description LIKE :q_description ';
    $term = '%' . $search . '%';
    $params = ['q_title' => $term, 'q_venue' => $term, 'q_description' => $term];
}
$countStmt = $pdo->prepare('SELECT COUNT(*) FROM events' . $where);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$sql = 'SELECT e.event_id, e.title, e.description, e.event_date, e.venue, e.capacity, e.created_at, e.poster_path,
               (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id AND r.status <> "cancelled") AS registration_count
        FROM events e' . ($search !== '' ? ' WHERE e.title LIKE :q_title OR e.venue LIKE :q_venue OR e.description LIKE :q_description ' : '') .
        ' ORDER BY e.event_date DESC LIMIT :limit OFFSET :offset';
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll();

$message = '';
if (isset($_GET['created'])) $message = 'Event created successfully.';
if (isset($_GET['updated'])) $message = 'Event updated successfully.';
if (isset($_GET['deleted'])) $message = 'Event deleted successfully.';
if (isset($_GET['deleteblocked'])) $message = 'This event has registrations and cannot be deleted. Cancel or remove its registrations first.';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Events - StudentHub</title><link rel="stylesheet" href="../css/style.css">
<style>
.admin-wrap{max-width:1200px;margin:2rem auto;padding:0 1rem}.panel{background:var(--card-bg,#fff);border:1px solid #ddd;border-radius:12px;padding:1.25rem;margin-bottom:1.2rem}
.toolbar{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between}.search-form{display:flex;gap:.5rem;flex-wrap:wrap}.search-form input{padding:.7rem;border:1px solid #bbb;border-radius:8px}
.table-wrap{overflow-x:auto}table{border-collapse:collapse;width:100%;min-width:950px}th,td{text-align:left;padding:.75rem;border-bottom:1px solid #ddd;vertical-align:top}th{background:#f2f5fa}.actions{display:flex;gap:.5rem;flex-wrap:wrap}.btn-small{display:inline-block;padding:.45rem .7rem;border:1px solid #aaa;border-radius:6px;text-decoration:none;color:inherit}.danger{border-color:#b42318;color:#b42318}.notice{padding:.8rem 1rem;border-radius:8px;background:#e8f7e8;margin:1rem 0}.pagination{display:flex;gap:.5rem;align-items:center;margin-top:1rem;flex-wrap:wrap}.muted{opacity:.7}
</style></head><body>
<header class="site-header"><div class="container nav-container"><a href="../index.html" class="logo">Student<span>Hub</span></a>
<nav class="main-nav"><a href="../admin-dashboard.php">Dashboard</a><a href="students.php">Manage Students</a><a href="events.php">Manage Events</a><a href="../logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Manage Events</h1><p>Admin-only event records: create, view, edit, search and delete.</p></div></section>
<div class="admin-wrap">
<?php if ($message !== ''): ?><div class="notice" role="status"><?= e_events($message) ?></div><?php endif; ?>
<div class="panel"><div class="toolbar"><div><h2>Event Records</h2><p class="muted"><?= $total ?> event(s) found</p></div>
<div class="actions"><a class="btn btn-primary" href="event-create.php">+ Add Event</a><a class="btn-small" href="../admin-dashboard.php">Back to Dashboard</a></div></div>
<form class="search-form" method="get" action="events.php"><input type="search" name="q" value="<?= e_events($search) ?>" placeholder="Search title, venue or description" aria-label="Search events">
<button class="btn btn-primary" type="submit">Search</button><?php if ($search !== ''): ?><a class="btn-small" href="events.php">Clear</a><?php endif; ?></form></div>
<div class="panel table-wrap"><table><thead><tr><th>ID</th><th>Poster</th><th>Title</th><th>Date and Time</th><th>Venue</th><th>Capacity</th><th>Registrations</th><th>Actions</th></tr></thead><tbody>
<?php if (!$events): ?><tr><td colspan="8">No events found.</td></tr><?php else: foreach ($events as $event): ?>
<tr><td><?= (int)$event['event_id'] ?></td><td><?php if (!empty($event['poster_path'])): ?><img src="../<?= e_events((string)$event['poster_path']) ?>" alt="Event poster" style="width:80px;height:80px;object-fit:cover;border-radius:6px"><?php else: ?><span class="muted">No poster</span><?php endif; ?></td><td><strong><?= e_events((string)$event['title']) ?></strong><br><span class="muted"><?= e_events(mb_strimwidth((string)$event['description'], 0, 100, '…')) ?></span></td>
<td><?= e_events((string)$event['event_date']) ?></td><td><?= e_events((string)$event['venue']) ?></td><td><?= (int)$event['capacity'] ?></td>
<td><?= (int)$event['registration_count'] ?></td><td><div class="actions"><a class="btn-small" href="event-edit.php?id=<?= (int)$event['event_id'] ?>">Edit</a>
<form method="post" action="event-delete.php" onsubmit="return confirm('Delete this event? Events with registrations cannot be deleted.');">
<input type="hidden" name="csrf_token" value="<?= e_events($_SESSION['events_csrf']) ?>"><input type="hidden" name="event_id" value="<?= (int)$event['event_id'] ?>">
<button class="btn-small danger" type="submit">Delete</button></form></div></td></tr>
<?php endforeach; endif; ?></tbody></table>
<div class="pagination"><?php if ($page > 1): ?><a class="btn-small" href="?q=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Previous</a><?php endif; ?>
<span>Page <?= $page ?> of <?= $totalPages ?></span><?php if ($page < $totalPages): ?><a class="btn-small" href="?q=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next</a><?php endif; ?></div></div>
</div></main><footer class="site-footer"><div class="container"><p>&copy; <?= date('Y') ?> StudentHub</p></div></footer><script src="../js/script.js"></script></body></html>
