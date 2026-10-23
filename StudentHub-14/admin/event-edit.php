<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/audit.php';
studenthub_require_role($pdo, 'admin');
if (empty($_SESSION['events_csrf'])) $_SESSION['events_csrf'] = bin2hex(random_bytes(32));
function e_event_edit(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Invalid event ID.'); }
$get = $pdo->prepare('SELECT event_id, title, description, event_date, venue, capacity FROM events WHERE event_id=:id LIMIT 1');
$get->execute(['id'=>$id]);
$event = $get->fetch();
if (!$event) { http_response_code(404); exit('Event not found.'); }

$title = (string)$event['title'];
$description = (string)$event['description'];
$eventDate = (new DateTimeImmutable((string)$event['event_date']))->format('Y-m-d\TH:i');
$venue = (string)$event['venue'];
$capacity = (string)$event['capacity'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $eventDate = trim((string)($_POST['event_date'] ?? ''));
    $venue = trim((string)($_POST['venue'] ?? ''));
    $capacityInput = (string)($_POST['capacity'] ?? '');
    $csrf = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['events_csrf'], $csrf)) $errors[] = 'Form session expired. Refresh and try again.';
    if ($title === '' || mb_strlen($title) > 150) $errors[] = 'Title is required and must be at most 150 characters.';
    if ($description === '' || mb_strlen($description) > 5000) $errors[] = 'Description is required and must be at most 5000 characters.';
    $date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $eventDate);
    if (!$date || $date->format('Y-m-d\TH:i') !== $eventDate) $errors[] = 'Enter a valid event date and time.';
    if ($venue === '' || mb_strlen($venue) > 200) $errors[] = 'Venue is required and must be at most 200 characters.';
    if (!ctype_digit($capacityInput) || (int)$capacityInput < 1 || (int)$capacityInput > 100000) $errors[] = 'Capacity must be between 1 and 100000.';
    $capacity = $capacityInput;

    if (!$errors) {
        $count = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id=:id AND status <> 'cancelled'");
        $count->execute(['id'=>$id]);
        if ((int)$count->fetchColumn() > (int)$capacity) $errors[] = 'Capacity cannot be less than the current number of active registrations.';
    }
    if (!$errors) {
        $update = $pdo->prepare('UPDATE events SET title=:title, description=:description, event_date=:event_date, venue=:venue, capacity=:capacity WHERE event_id=:id');
        $update->execute(['title'=>$title,'description'=>$description,'event_date'=>$date->format('Y-m-d H:i:s'),'venue'=>$venue,'capacity'=>(int)$capacity,'id'=>$id]);
        studenthub_audit($pdo, 'UPDATE', 'event', (int)$id, 'Updated event: ' . $title);
        header('Location: events.php?updated=1');
        exit;
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Edit Event - StudentHub</title><link rel="stylesheet" href="../css/style.css">
<style>.form-wrap{max-width:720px;margin:2rem auto;padding:0 1rem}.form-card{padding:1.5rem;border:1px solid #ddd;border-radius:12px;background:var(--card-bg,#fff)}.form-group{margin:1rem 0}.form-group label{display:block;margin-bottom:.4rem}.form-group input,.form-group textarea{box-sizing:border-box;width:100%;padding:.75rem;border:1px solid #aaa;border-radius:8px}.form-group textarea{min-height:130px}.errors{padding:1rem;background:#fff0f0;color:#812020;border-radius:8px}.actions{display:flex;gap:.75rem;flex-wrap:wrap}</style></head><body>
<header class="site-header"><div class="container nav-container"><a href="../index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="events.php">Manage Events</a><a href="../logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Edit Event #<?=(int)$id?></h1><p>Update event details.</p></div></section><div class="form-wrap"><div class="form-card">
<?php if ($errors): ?><div class="errors" role="alert"><strong>Please fix:</strong><ul><?php foreach($errors as $err): ?><li><?=e_event_edit($err)?></li><?php endforeach;?></ul></div><?php endif;?>
<form method="post" action="event-edit.php?id=<?=(int)$id?>"><input type="hidden" name="csrf_token" value="<?=e_event_edit($_SESSION['events_csrf'])?>">
<div class="form-group"><label for="title">Event title</label><input id="title" name="title" value="<?=e_event_edit($title)?>" required maxlength="150"></div>
<div class="form-group"><label for="description">Description</label><textarea id="description" name="description" required maxlength="5000"><?=e_event_edit($description)?></textarea></div>
<div class="form-group"><label for="event_date">Date and time</label><input id="event_date" type="datetime-local" name="event_date" value="<?=e_event_edit($eventDate)?>" required></div>
<div class="form-group"><label for="venue">Venue</label><input id="venue" name="venue" value="<?=e_event_edit($venue)?>" required maxlength="200"></div>
<div class="form-group"><label for="capacity">Capacity</label><input id="capacity" type="number" name="capacity" min="1" max="100000" value="<?=e_event_edit($capacity)?>" required></div>
<div class="actions"><button class="btn btn-primary" type="submit">Save Changes</button><a class="btn-small" href="events.php">Cancel</a></div></form>
</div></div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer><script src="../js/script.js"></script></body></html>
