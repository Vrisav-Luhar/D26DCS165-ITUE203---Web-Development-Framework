<?php
declare(strict_types=1);
require_once __DIR__ . '/config/auth.php';

studenthub_restore_remembered_session($pdo);
studenthub_enforce_session_timeout($pdo);

if (!empty($_SESSION['student_id'])) {
    header('Location: ' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin-dashboard.php' : 'student-dashboard.php'));
    exit;
}

if (empty($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $csrf = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['login_csrf'], $csrf)) {
        $error = 'Your form session expired. Refresh and try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT student_id, full_name, email, password_hash, role
             FROM students WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || empty($user['password_hash']) || !password_verify($password, (string)$user['password_hash'])) {
            // Keep the same response for unknown email and wrong password.
            $error = 'Invalid email or password.';
        } else {
            session_regenerate_id(true);
            $_SESSION['student_id'] = (int)$user['student_id'];
            $_SESSION['student_name'] = (string)$user['full_name'];
            $_SESSION['student_email'] = (string)$user['email'];
            $_SESSION['role'] = (string)$user['role'];
            $_SESSION['last_activity'] = time();
            $_SESSION['login_csrf'] = bin2hex(random_bytes(32));

            if (!empty($_POST['remember_me'])) {
                studenthub_issue_remember_token($pdo, (int)$user['student_id']);
            }

            $target = $user['role'] === 'admin' ? 'admin-dashboard.php' : 'student-dashboard.php';
            header('Location: ' . $target);
            exit;
        }
    }
}
function e_login(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StudentHub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .auth-wrap{max-width:520px;margin:2rem auto;padding:0 1rem}
        .auth-card{padding:2rem;border:1px solid #ddd;border-radius:14px;background:var(--card-bg,#fff);box-shadow:0 8px 24px #0000000d}
        .auth-card .form-group{margin-bottom:1rem}
        .auth-card label{display:block;margin-bottom:.4rem}
        .auth-card input[type=email],.auth-card input[type=password]{width:100%;padding:.8rem;border:1px solid #bbb;border-radius:8px}
        .auth-card .remember{display:flex;align-items:center;gap:.5rem;margin:1rem 0}
        .auth-alert{padding:.85rem 1rem;border-radius:8px;margin:1rem 0;background:#fff0f0;color:#8a1f1f}
        .auth-note{font-size:.9rem;opacity:.8}
    </style>
</head>
<body>
<header class="site-header">
    <div class="container nav-container">
        <a href="index.html" class="logo">Student<span>Hub</span></a>
        <button class="hamburger" id="hamburger" aria-label="Open navigation menu" aria-expanded="false">☰</button>
        <nav class="main-nav" id="mainNav">
            <a href="index.html">Home</a><a href="events.html">Events</a>
            <a href="register.php">Register</a><a href="contact.php">Contact</a>
            <button id="themeToggle" class="theme-toggle" type="button" aria-label="Toggle dark mode">🌙</button>
        </nav>
    </div>
</header>
<main>
    <section class="page-header"><div class="container"><h1>Login to StudentHub</h1><p>Secure session-based authentication</p></div></section>
    <div class="auth-wrap"><div class="auth-card">
        <h2>Welcome Back</h2>
        <?php if (isset($_GET['timeout'])): ?><p class="auth-alert" role="alert">You were logged out because your session was inactive for too long.</p><?php endif; ?>
        <?php if (isset($_GET['required'])): ?><p class="auth-alert" role="alert">Please log in to access that page.</p><?php endif; ?>
        <?php if ($error !== ''): ?><p class="auth-alert" role="alert"><?= e_login($error) ?></p><?php endif; ?>
        <form method="post" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= e_login($_SESSION['login_csrf']) ?>">
            <div class="form-group"><label for="email">Email Address</label>
                <input type="email" id="email" name="email" autocomplete="username" maxlength="150" required value="<?= e_login($email) ?>"></div>
            <div class="form-group"><label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required></div>
            <label class="remember"><input type="checkbox" name="remember_me" value="1"> Remember me for 30 days on this device</label>
            <button class="btn btn-primary" type="submit">Login</button>
        </form>
        <p class="auth-note">Sessions expire after 15 minutes of inactivity. Do not select Remember me on a shared computer.</p>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div></div>
</main>
<footer class="site-footer"><div class="container"><p>&copy; <?= date('Y') ?> StudentHub</p></div></footer>
<script src="js/script.js"></script>
</body>
</html>
