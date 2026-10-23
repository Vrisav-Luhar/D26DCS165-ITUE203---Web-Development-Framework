<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';

if (!empty($_COOKIE[STUDENTHUB_REMEMBER_COOKIE])) {
    $parts = explode(':', (string)$_COOKIE[STUDENTHUB_REMEMBER_COOKIE], 2);
    if (count($parts) === 2 && preg_match('/^[a-f0-9]{24}$/', $parts[0])) {
        studenthub_delete_remember_token($pdo, $parts[0]);
    }
}
studenthub_clear_remember_cookie();

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'] ?: '/',
        'domain' => $params['domain'] ?? '',
        'secure' => (bool)$params['secure'],
        'httponly' => (bool)$params['httponly'],
        'samesite' => 'Lax',
    ]);
}
session_destroy();
header('Location: login.php?logged_out=1');
exit;
