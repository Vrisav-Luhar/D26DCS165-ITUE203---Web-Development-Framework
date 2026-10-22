<?php
declare(strict_types=1);

/**
 * StudentHub Practical 13 JSON API bootstrap.
 * All endpoints return JSON and require an authenticated Admin.
 */
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function api_json(int $status, array $payload): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_read_json(): array {
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        api_json(400, ['success' => false, 'message' => 'Request body must contain JSON.']);
    }
    try {
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        api_json(400, ['success' => false, 'message' => 'Invalid JSON request body.']);
    }
    if (!is_array($data)) {
        api_json(400, ['success' => false, 'message' => 'JSON body must be an object.']);
    }
    return $data;
}

function api_require_method(string $method): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $method) {
        header('Allow: ' . $method);
        api_json(405, ['success' => false, 'message' => 'Method not allowed.']);
    }
}

function api_require_csrf(): void {
    $sent = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $expected = (string)($_SESSION['api_csrf'] ?? '');
    if ($expected === '' || $sent === '' || !hash_equals($expected, $sent)) {
        api_json(403, ['success' => false, 'message' => 'Invalid or missing CSRF token. Refresh the API demo page and try again.']);
    }
}

function api_require_admin(): void {
    studenthub_require_role($GLOBALS['pdo'], 'admin');
}

api_require_admin();
if (empty($_SESSION['api_csrf'])) {
    $_SESSION['api_csrf'] = bin2hex(random_bytes(32));
}
