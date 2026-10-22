<?php
declare(strict_types=1);

/**
 * StudentHub Practical 10 authentication helpers.
 * Include this file only from PHP pages that need authentication.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/db.php';

const STUDENTHUB_SESSION_TIMEOUT = 900; // 15 minutes of inactivity
const STUDENTHUB_REMEMBER_COOKIE = 'studenthub_remember';

function studenthub_clear_remember_cookie(): void {
    setcookie(STUDENTHUB_REMEMBER_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function studenthub_delete_remember_token(PDO $pdo, string $selector): void {
    $stmt = $pdo->prepare('DELETE FROM remember_me_tokens WHERE selector = :selector');
    $stmt->execute(['selector' => $selector]);
}

function studenthub_issue_remember_token(PDO $pdo, int $studentId): void {
    $selector = bin2hex(random_bytes(12));       // 24 characters
    $validator = bin2hex(random_bytes(32));      // 64 characters
    $expires = (new DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare(
        'INSERT INTO remember_me_tokens (student_id, selector, validator_hash, expires_at)
         VALUES (:student_id, :selector, :validator_hash, :expires_at)'
    );
    $stmt->execute([
        'student_id' => $studentId,
        'selector' => $selector,
        'validator_hash' => hash('sha256', $validator),
        'expires_at' => $expires,
    ]);

    setcookie(STUDENTHUB_REMEMBER_COOKIE, $selector . ':' . $validator, [
        'expires' => time() + (30 * 24 * 60 * 60),
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function studenthub_restore_remembered_session(PDO $pdo): void {
    if (!empty($_SESSION['student_id']) || empty($_COOKIE[STUDENTHUB_REMEMBER_COOKIE])) {
        return;
    }

    $parts = explode(':', (string)$_COOKIE[STUDENTHUB_REMEMBER_COOKIE], 2);
    if (count($parts) !== 2 ||
        !preg_match('/^[a-f0-9]{24}$/', $parts[0]) ||
        !preg_match('/^[a-f0-9]{64}$/', $parts[1])) {
        studenthub_clear_remember_cookie();
        return;
    }

    [$selector, $validator] = $parts;
    $stmt = $pdo->prepare(
        'SELECT t.student_id, t.validator_hash, t.expires_at,
                s.full_name, s.email, s.role
         FROM remember_me_tokens t
         JOIN students s ON s.student_id = t.student_id
         WHERE t.selector = :selector LIMIT 1'
    );
    $stmt->execute(['selector' => $selector]);
    $row = $stmt->fetch();

    if (!$row ||
        strtotime((string)$row['expires_at']) <= time() ||
        !hash_equals((string)$row['validator_hash'], hash('sha256', $validator))) {
        studenthub_delete_remember_token($pdo, $selector);
        studenthub_clear_remember_cookie();
        return;
    }

    session_regenerate_id(true);
    $_SESSION['student_id'] = (int)$row['student_id'];
    $_SESSION['student_name'] = (string)$row['full_name'];
    $_SESSION['student_email'] = (string)$row['email'];
    $_SESSION['role'] = (string)$row['role'];
    $_SESSION['last_activity'] = time();

    // Rotate the token after successful use to reduce replay risk.
    studenthub_delete_remember_token($pdo, $selector);
    studenthub_clear_remember_cookie();
    studenthub_issue_remember_token($pdo, (int)$row['student_id']);
}

function studenthub_enforce_session_timeout(PDO $pdo): void {
    if (empty($_SESSION['student_id'])) {
        return;
    }

    $lastActivity = (int)($_SESSION['last_activity'] ?? time());
    if (time() - $lastActivity > STUDENTHUB_SESSION_TIMEOUT) {
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
        studenthub_clear_remember_cookie();
        header('Location: login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function studenthub_require_login(PDO $pdo): void {
    studenthub_restore_remembered_session($pdo);
    studenthub_enforce_session_timeout($pdo);

    if (empty($_SESSION['student_id'])) {
        header('Location: login.php?required=1');
        exit;
    }
}

function studenthub_require_role(PDO $pdo, string $role): void {
    studenthub_require_login($pdo);
    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        $home = ($_SESSION['role'] ?? '') === 'admin' ? 'admin-dashboard.php' : 'student-dashboard.php';
        echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Access denied</title>';
        echo '<body style="font-family:Arial,sans-serif;max-width:680px;margin:4rem auto;padding:1rem">';
        echo '<h1>403 — Access denied</h1><p>You do not have permission to view this page.</p>';
        echo '<p><a href="' . htmlspecialchars($home, ENT_QUOTES, 'UTF-8') . '">Return to your dashboard</a></p></body></html>';
        exit;
    }
}
