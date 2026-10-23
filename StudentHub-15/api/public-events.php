<?php
declare(strict_types=1);

/**
 * Public, read-only event listing for the StudentHub student-facing Events page.
 * Admin write operations remain protected in api/events.php.
 */
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

try {
    $stmt = $pdo->query(
        'SELECT event_id, title, description, event_date, venue, capacity, poster_path
         FROM events
         ORDER BY event_date ASC, event_id ASC
         LIMIT 200'
    );
    echo json_encode(
        ['success' => true, 'data' => $stmt->fetchAll()],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
} catch (PDOException $exception) {
    error_log('StudentHub public events API failed: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to load events. Please try again later.']);
}
