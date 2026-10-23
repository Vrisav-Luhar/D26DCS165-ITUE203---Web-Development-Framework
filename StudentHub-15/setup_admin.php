<?php
declare(strict_types=1);
require_once __DIR__ . '/config/db.php';

$errors = [];
$success = '';
$email = '';
$name = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (mb_strlen($name) < 3 || mb_strlen($name) > 100) $errors[] = 'Name must be 3–100 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number and special character.';
    }
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $check = $pdo->prepare('SELECT student_id FROM students WHERE email = :email LIMIT 1');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $errors[] = 'This email already exists. Use a different email or change the existing account role manually in phpMyAdmin.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO students (full_name, email, course, study_year, password_hash, role)
                 VALUES (:name, :email, 'StudentHub Administrator', 1, :hash, 'admin')"
            );
            $stmt->execute(['name' => $name, 'email' => $email, 'hash' => password_hash($password, PASSWORD_DEFAULT)]);
            $success = 'Admin account created. IMPORTANT: delete setup_admin.php now, then log in at login.php.';
            $name = $email = '';
        }
    }
}
function e_setup(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>One-time Admin Setup</title>
<link rel="stylesheet" href="css/style.css"><style>.setup{max-width:520px;margin:3rem auto;padding:1.5rem;border:1px solid #ddd;border-radius:12px}.setup label{display:block;margin:.8rem 0 .3rem}.setup input{width:100%;padding:.75rem}.msg{padding:1rem;background:#fff0f0;margin:1rem 0}.ok{background:#e8f7e8}</style></head><body>
<div class="setup"><h1>One-time Admin Setup</h1><p>Run this locally only. Delete this file immediately after creating the admin account.</p>
<?php foreach($errors as $error): ?><p class="msg"><?=e_setup($error)?></p><?php endforeach;?>
<?php if($success): ?><p class="msg ok"><?=e_setup($success)?></p><?php endif;?>
<form method="post"><label for="name">Admin full name</label><input id="name" name="name" required minlength="3" maxlength="100" value="<?=e_setup($name)?>">
<label for="email">Admin email</label><input id="email" name="email" type="email" required value="<?=e_setup($email)?>">
<label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="new-password">
<label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" required autocomplete="new-password">
<p><button type="submit" class="btn btn-primary">Create Admin Account</button></p></form>
</div></body></html>
