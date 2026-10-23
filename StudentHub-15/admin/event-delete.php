<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/event-poster.php';
studenthub_require_role($pdo, 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit;
}
$csrf = (string)($_POST['csrf_token'] ?? '');
if (empty($_SESSION['events_csrf']) || !hash_equals($_SESSION['events_csrf'], $csrf)) {
    http_response_code(403);
    exit('Invalid form token. Return to Manage Events and try again.');
}
$id = filter_var($_POST['event_id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid event ID.');
}
$count = $pdo->prepare('SELECT COUNT(*) FROM registrations WHERE event_id = :id');
$count->execute(['id'=>$id]);
if ((int)$count->fetchColumn() > 0) {
    header('Location: events.php?deleteblocked=1');
    exit;
}
try {
    $before = $pdo->prepare('SELECT title, poster_path FROM events WHERE event_id = :id LIMIT 1');
    $before->execute(['id' => $id]);
    $oldEvent = $before->fetch();
    $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = :id');
    $stmt->execute(['id'=>$id]);
    if ($stmt->rowCount() > 0) {
        studenthub_audit($pdo, 'DELETE', 'event', (int)$id, 'Deleted event: ' . (string)($oldEvent['title'] ?? 'Unknown event'));
        studenthub_delete_event_poster((string)($oldEvent['poster_path'] ?? ''));
    }
    header('Location: events.php?deleted=1');
    exit;
} catch (PDOException $ex) {
    error_log('StudentHub delete event: '.$ex->getMessage());
    http_response_code(500);
    exit('Could not delete this event. Check whether related registrations exist.');
}
