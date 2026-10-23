<?php
declare(strict_types=1);

/*
 * Public read-only event feed for the student-facing Events page.
 * The Admin CRUD pages and this endpoint use the same `events` table.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    require_once __DIR__ . '/../config/db.php';

    $stmt = $pdo->query(
        'SELECT event_id, title, description, event_date, venue, capacity, poster_path
         FROM events
         ORDER BY event_date ASC, event_id ASC'
    );

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prefix relative poster paths from the project root for events.html.
    foreach ($events as &$event) {
        $event['event_id'] = (int)$event['event_id'];
        $event['capacity'] = $event['capacity'] === null ? null : (int)$event['capacity'];
        if (!empty($event['poster_path'])) {
            $event['poster_path'] = ltrim((string)$event['poster_path'], '/');
        } else {
            $event['poster_path'] = null;
        }
    }
    unset($event);

    echo json_encode([
        'success' => true,
        'events' => $events,
        'count' => count($events),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    error_log('StudentHub public events feed failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to load events at this time.',
        'events' => [],
    ]);
}
