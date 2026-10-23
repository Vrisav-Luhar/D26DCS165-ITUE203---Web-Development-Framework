<?php
declare(strict_types=1);

/**
 * Public, read-only event feed for the student-facing Events page.
 * It intentionally does not include the admin-only API bootstrap.
 */
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $stmt = $pdo->query(
        'SELECT event_id, title, description, event_date, venue, capacity
         FROM events
         ORDER BY event_date ASC, event_id ASC
         LIMIT 200'
    );
    echo json_encode(
        ['success' => true, 'data' => $stmt->fetchAll()],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
} catch (Throwable $exception) {
    error_log('StudentHub public events feed failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Events could not be loaded. Check the database connection and events table.'
    ]);
}
