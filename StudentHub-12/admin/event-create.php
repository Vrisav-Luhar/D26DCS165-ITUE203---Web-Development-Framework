<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/poster-upload-helper.php';
studenthub_require_role($pdo, 'admin');
if (empty($_SESSION['events_csrf'])) $_SESSION['events_csrf'] = bin2hex(random_bytes(32));

function e_event_create(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
$errors = [];
$title = $description = $eventDate = $venue = '';
$capacity = '50';

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

    $posterPath = studenthub_store_event_poster($_FILES['poster'] ?? [], $errors);
    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO events (title, description, event_date, venue, capacity, poster_path) VALUES (:title, :description, :event_date, :venue, :capacity, :poster_path)');
            $stmt->execute([
                'title' => $title, 'description' => $description,
                'event_date' => $date->format('Y-m-d H:i:s'), 'venue' => $venue, 'capacity' => (int)$capacity, 'poster_path' => $posterPath,
            ]);
            header('Location: events.php?created=1');
            exit;
        } catch (Throwable $e) {
            studenthub_delete_event_poster($posterPath);
            $errors[] = 'Could not save the event. Ensure the poster_path database migration has been applied.';
        }
    } else { studenthub_delete_event_poster($posterPath); }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Add Event - StudentHub</title><link rel="stylesheet" href="../css/style.css">
<style>.form-wrap{max-width:720px;margin:2rem auto;padding:0 1rem}.form-card{padding:1.5rem;border:1px solid #ddd;border-radius:12px;background:var(--card-bg,#fff)}.form-group{margin:1rem 0}.form-group label{display:block;margin-bottom:.4rem}.form-group input,.form-group textarea{box-sizing:border-box;width:100%;padding:.75rem;border:1px solid #aaa;border-radius:8px}.form-group textarea{min-height:130px}.errors{padding:1rem;background:#fff0f0;color:#812020;border-radius:8px}.actions{display:flex;gap:.75rem;flex-wrap:wrap}</style></head><body>
<header class="site-header"><div class="container nav-container"><a href="../index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="events.php">Manage Events</a><a href="../logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Add Event</h1><p>Create a new event for StudentHub.</p></div></section><div class="form-wrap"><div class="form-card">
<?php if ($errors): ?><div class="errors" role="alert"><strong>Please fix:</strong><ul><?php foreach($errors as $err): ?><li><?=e_event_create($err)?></li><?php endforeach;?></ul></div><?php endif;?>
<form method="post" enctype="multipart/form-data" action="event-create.php"><input type="hidden" name="csrf_token" value="<?=e_event_create($_SESSION['events_csrf'])?>">
<div class="form-group"><label for="title">Event title</label><input id="title" name="title" value="<?=e_event_create($title)?>" required maxlength="150"></div>
<div class="form-group"><label for="description">Description</label><textarea id="description" name="description" required maxlength="5000"><?=e_event_create($description)?></textarea></div>
<div class="form-group"><label for="event_date">Date and time</label><input id="event_date" type="datetime-local" name="event_date" value="<?=e_event_create($eventDate)?>" required></div>
<div class="form-group"><label for="venue">Venue</label><input id="venue" name="venue" value="<?=e_event_create($venue)?>" required maxlength="200"></div>
<div class="form-group"><label for="poster">Event poster (optional; JPG, PNG or WEBP, maximum 2 MB)</label><input id="poster" type="file" name="poster" accept="image/jpeg,image/png,image/webp"></div>
<div class="form-group"><label for="capacity">Capacity</label><input id="capacity" type="number" name="capacity" min="1" max="100000" value="<?=e_event_create($capacity)?>" required></div>
<div class="actions"><button class="btn btn-primary" type="submit">Create Event</button><a class="btn-small" href="events.php">Cancel</a></div></form>
</div></div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer><script src="../js/script.js"></script></body></html>
