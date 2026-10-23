<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
studenthub_require_role($pdo, 'admin');

if (empty($_SESSION['students_csrf'])) $_SESSION['students_csrf'] = bin2hex(random_bytes(32));
$errors = [];
$name = $email = $mobile = $course = '';
$year = '1';

function e_create(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }

$courses = ['B.Tech Computer Science', 'B.Tech Information Technology', 'B.Tech AI & Machine Learning',
            'B.Tech Computer Engineering', 'Diploma Information Technology', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $mobile = trim((string)($_POST['mobile'] ?? ''));
    $course = trim((string)($_POST['course'] ?? ''));
    $yearInput = (string)($_POST['study_year'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $csrf = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['students_csrf'], $csrf)) $errors[] = 'Form session expired. Refresh and try again.';
    if (mb_strlen($name) < 3 || mb_strlen($name) > 100 || !preg_match("/^[\p{L}][\p{L}\s.'-]{2,99}$/u", $name)) $errors[] = 'Enter a valid full name (3–100 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) $errors[] = 'Enter a valid email address.';
    if ($mobile !== '' && !preg_match('/^[0-9]{10,15}$/', $mobile)) $errors[] = 'Mobile must contain 10–15 digits.';
    if (!in_array($course, $courses, true)) $errors[] = 'Select a valid course.';
    if (!ctype_digit($yearInput) || (int)$yearInput < 1 || (int)$yearInput > 6) $errors[] = 'Select a valid study year.';
    $year = $yearInput;
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) $errors[] = 'Password must be 8+ characters with uppercase, lowercase, number and special character.';

    if (!$errors) {
        $check = $pdo->prepare('SELECT student_id FROM students WHERE email = :email LIMIT 1');
        $check->execute(['email' => $email]);
        if ($check->fetch()) $errors[] = 'This email address is already registered.';
        else {
            try {
                $insert = $pdo->prepare(
                    "INSERT INTO students (full_name, email, mobile, course, study_year, password_hash, role)
                     VALUES (:name, :email, :mobile, :course, :year, :password_hash, 'student')"
                );
                $insert->execute([
                    'name' => $name, 'email' => $email, 'mobile' => $mobile !== '' ? $mobile : null,
                    'course' => $course, 'year' => (int)$year, 'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);
                header('Location: students.php?created=1');
                exit;
            } catch (PDOException $ex) {
                error_log('StudentHub admin create student: ' . $ex->getMessage());
                $errors[] = $ex->getCode() === '23000' ? 'This email address is already registered.' : 'Could not save the student. Please try again.';
            }
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Add Student - StudentHub</title><link rel="stylesheet" href="../css/style.css">
<style>.form-wrap{max-width:680px;margin:2rem auto;padding:0 1rem}.form-card{padding:1.5rem;border:1px solid #ddd;border-radius:12px;background:var(--card-bg,#fff)}.form-group{margin:1rem 0}.form-group label{display:block;margin-bottom:.4rem}.form-group input,.form-group select{box-sizing:border-box;width:100%;padding:.75rem;border:1px solid #aaa;border-radius:8px}.errors{padding:1rem;background:#fff0f0;color:#812020;border-radius:8px}.actions{display:flex;gap:.75rem;flex-wrap:wrap}</style></head><body>
<header class="site-header"><div class="container nav-container"><a href="../index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="students.php">Manage Students</a><a href="../logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Add Student</h1><p>Create a student account from the admin panel.</p></div></section>
<div class="form-wrap"><div class="form-card">
<?php if ($errors): ?><div class="errors" role="alert"><strong>Please fix:</strong><ul><?php foreach ($errors as $err): ?><li><?= e_create($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="student-create.php">
<input type="hidden" name="csrf_token" value="<?= e_create($_SESSION['students_csrf']) ?>">
<div class="form-group"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="<?= e_create($name) ?>" required minlength="3" maxlength="100"></div>
<div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" value="<?= e_create($email) ?>" required maxlength="150"></div>
<div class="form-group"><label for="mobile">Mobile (optional)</label><input id="mobile" name="mobile" value="<?= e_create($mobile) ?>" inputmode="numeric" pattern="[0-9]{10,15}"></div>
<div class="form-group"><label for="course">Course</label><select id="course" name="course" required><option value="">Select course</option><?php foreach($courses as $c): ?><option value="<?= e_create($c) ?>" <?= $course===$c?'selected':'' ?>><?= e_create($c) ?></option><?php endforeach; ?></select></div>
<div class="form-group"><label for="study_year">Study year</label><select id="study_year" name="study_year" required><option value="">Select year</option><?php for($i=1;$i<=6;$i++): ?><option value="<?=$i?>" <?= $year===(string)$i?'selected':'' ?>>Year <?=$i?></option><?php endfor; ?></select></div>
<div class="form-group"><label for="password">Temporary password</label><input id="password" type="password" name="password" required autocomplete="new-password"><small>At least 8 characters with uppercase, lowercase, number and special character.</small></div>
<div class="actions"><button class="btn btn-primary" type="submit">Create Student</button><a class="btn-small" href="students.php">Cancel</a></div>
</form></div></div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer><script src="../js/script.js"></script></body></html>
