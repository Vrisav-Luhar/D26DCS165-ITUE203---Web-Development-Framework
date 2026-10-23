<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/audit.php';
studenthub_require_role($pdo, 'admin');
if (empty($_SESSION['students_csrf'])) $_SESSION['students_csrf'] = bin2hex(random_bytes(32));
function e_edit(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Invalid student ID.'); }
$get = $pdo->prepare('SELECT student_id, full_name, email, mobile, course, study_year, role FROM students WHERE student_id = :id LIMIT 1');
$get->execute(['id' => $id]);
$student = $get->fetch();
if (!$student) { http_response_code(404); exit('Student record not found.'); }

$errors = [];
$name = (string)$student['full_name'];
$email = (string)$student['email'];
$mobile = (string)($student['mobile'] ?? '');
$course = (string)$student['course'];
$year = (string)$student['study_year'];
$courses = ['B.Tech Computer Science', 'B.Tech Information Technology', 'B.Tech AI & Machine Learning',
            'B.Tech Computer Engineering', 'Diploma Information Technology', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $mobile = trim((string)($_POST['mobile'] ?? ''));
    $course = trim((string)($_POST['course'] ?? ''));
    $yearInput = (string)($_POST['study_year'] ?? '');
    $newPassword = (string)($_POST['new_password'] ?? '');
    $newRole = (string)($_POST['role'] ?? 'student');
    $csrf = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['students_csrf'], $csrf)) $errors[] = 'Form session expired. Refresh and try again.';
    if (mb_strlen($name) < 3 || mb_strlen($name) > 100 || !preg_match("/^[\p{L}][\p{L}\s.'-]{2,99}$/u", $name)) $errors[] = 'Enter a valid full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) $errors[] = 'Enter a valid email.';
    if ($mobile !== '' && !preg_match('/^[0-9]{10,15}$/', $mobile)) $errors[] = 'Mobile must contain 10–15 digits.';
    if (!in_array($course, $courses, true)) $errors[] = 'Select a valid course.';
    if (!ctype_digit($yearInput) || (int)$yearInput < 1 || (int)$yearInput > 6) $errors[] = 'Select a valid study year.';
    if (!in_array($newRole, ['student', 'admin'], true)) $errors[] = 'Invalid role.';
    if ((int)$id === (int)$_SESSION['student_id'] && $newRole !== 'admin') $errors[] = 'You cannot change your own role.';
    if ($newPassword !== '' && (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[^A-Za-z0-9]/', $newPassword))) $errors[] = 'New password must be 8+ characters with uppercase, lowercase, number and special character.';

    if (!$errors) {
        $check = $pdo->prepare('SELECT student_id FROM students WHERE email = :email AND student_id <> :id LIMIT 1');
        $check->execute(['email' => $email, 'id' => $id]);
        if ($check->fetch()) $errors[] = 'Another account already uses this email.';
    }
    if (!$errors) {
        try {
            $sql = 'UPDATE students SET full_name=:name, email=:email, mobile=:mobile, course=:course, study_year=:year, role=:role';
            $params = ['name'=>$name, 'email'=>$email, 'mobile'=>$mobile!==''?$mobile:null, 'course'=>$course, 'year'=>(int)$yearInput, 'role'=>$newRole, 'id'=>$id];
            if ($newPassword !== '') {
                $sql .= ', password_hash=:password_hash';
                $params['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
            $sql .= ' WHERE student_id=:id';
            $update = $pdo->prepare($sql);
            $update->execute($params);
            studenthub_audit($pdo, 'UPDATE', 'student', (int)$id, 'Updated student account: ' . $email . '; role=' . $newRole);
            header('Location: students.php?updated=1');
            exit;
        } catch (PDOException $ex) {
            error_log('StudentHub admin edit student: ' . $ex->getMessage());
            $errors[] = $ex->getCode() === '23000' ? 'Email or role update conflicts with a database constraint.' : 'Could not update this record. Please try again.';
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Student - StudentHub</title><link rel="stylesheet" href="../css/style.css">
<style>.form-wrap{max-width:680px;margin:2rem auto;padding:0 1rem}.form-card{padding:1.5rem;border:1px solid #ddd;border-radius:12px;background:var(--card-bg,#fff)}.form-group{margin:1rem 0}.form-group label{display:block;margin-bottom:.4rem}.form-group input,.form-group select{box-sizing:border-box;width:100%;padding:.75rem;border:1px solid #aaa;border-radius:8px}.errors{padding:1rem;background:#fff0f0;color:#812020;border-radius:8px}.actions{display:flex;gap:.75rem;flex-wrap:wrap}</style></head><body>
<header class="site-header"><div class="container nav-container"><a href="../index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="students.php">Manage Students</a><a href="../logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Edit Student #<?= (int)$id ?></h1><p>Update account details. Leave new password blank to keep the current password.</p></div></section>
<div class="form-wrap"><div class="form-card">
<?php if ($errors): ?><div class="errors" role="alert"><strong>Please fix:</strong><ul><?php foreach($errors as $err): ?><li><?=e_edit($err)?></li><?php endforeach;?></ul></div><?php endif;?>
<form method="post" action="student-edit.php?id=<?=(int)$id?>">
<input type="hidden" name="csrf_token" value="<?=e_edit($_SESSION['students_csrf'])?>">
<div class="form-group"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="<?=e_edit($name)?>" required minlength="3" maxlength="100"></div>
<div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" value="<?=e_edit($email)?>" required maxlength="150"></div>
<div class="form-group"><label for="mobile">Mobile (optional)</label><input id="mobile" name="mobile" value="<?=e_edit($mobile)?>" pattern="[0-9]{10,15}"></div>
<div class="form-group"><label for="course">Course</label><select id="course" name="course" required><?php foreach($courses as $c):?><option value="<?=e_edit($c)?>" <?=$course===$c?'selected':''?>><?=e_edit($c)?></option><?php endforeach;?></select></div>
<div class="form-group"><label for="study_year">Study year</label><select id="study_year" name="study_year" required><?php for($i=1;$i<=6;$i++):?><option value="<?=$i?>" <?=$year===(string)$i?'selected':''?>>Year <?=$i?></option><?php endfor;?></select></div>
<div class="form-group"><label for="role">Role</label><select id="role" name="role" required><option value="student" <?=$student['role']==='student'?'selected':''?>>Student</option><option value="admin" <?=$student['role']==='admin'?'selected':''?>>Admin</option></select><small>Only grant Admin role to trusted accounts.</small></div>
<div class="form-group"><label for="new_password">Set new password (optional)</label><input id="new_password" type="password" name="new_password" autocomplete="new-password"><small>Leave blank to retain the existing password.</small></div>
<div class="actions"><button class="btn btn-primary" type="submit">Save Changes</button><a class="btn-small" href="students.php">Cancel</a></div>
</form></div></div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer><script src="../js/script.js"></script></body></html>
