<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
studenthub_require_role($pdo, 'admin');

if (empty($_SESSION['students_csrf'])) {
    $_SESSION['students_csrf'] = bin2hex(random_bytes(32));
}
function e_students(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$search = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];
if ($search !== '') {
    $where = ' WHERE full_name LIKE :q_name OR email LIKE :q_email OR course LIKE :q_course ';
    $term = '%' . $search . '%';
    $params = ['q_name' => $term, 'q_email' => $term, 'q_course' => $term];
}
$countStmt = $pdo->prepare('SELECT COUNT(*) FROM students' . $where);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$sql = 'SELECT student_id, full_name, email, mobile, course, study_year, role, created_at
        FROM students' . $where . ' ORDER BY student_id DESC LIMIT :limit OFFSET :offset';
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll();

$message = '';
if (isset($_GET['created'])) $message = 'Student record created successfully.';
if (isset($_GET['updated'])) $message = 'Student record updated successfully.';
if (isset($_GET['deleted'])) $message = 'Student record deleted successfully.';
if (isset($_GET['roleblocked'])) $message = 'Your own Admin account cannot be deleted or changed to Student here.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Students - StudentHub</title><link rel="stylesheet" href="../css/style.css">
<style>
.admin-wrap{max-width:1200px;margin:2rem auto;padding:0 1rem}.panel{background:var(--card-bg,#fff);border:1px solid #ddd;border-radius:12px;padding:1.25rem;margin-bottom:1.2rem}
.toolbar{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between}.search-form{display:flex;gap:.5rem;flex-wrap:wrap}.search-form input{padding:.7rem;border:1px solid #bbb;border-radius:8px}
.table-wrap{overflow-x:auto}table{border-collapse:collapse;width:100%;min-width:850px}th,td{text-align:left;padding:.75rem;border-bottom:1px solid #ddd}th{background:#f2f5fa}.actions{display:flex;gap:.5rem;flex-wrap:wrap}.btn-small{display:inline-block;padding:.45rem .7rem;border:1px solid #aaa;border-radius:6px;text-decoration:none;color:inherit}.danger{border-color:#b42318;color:#b42318}.notice{padding:.8rem 1rem;border-radius:8px;background:#e8f7e8;margin:1rem 0}.pagination{display:flex;gap:.5rem;align-items:center;margin-top:1rem;flex-wrap:wrap}.muted{opacity:.7}
</style>
</head>
<body>
<header class="site-header"><div class="container nav-container">
<a href="../index.html" class="logo">Student<span>Hub</span></a>
<nav class="main-nav"><a href="../admin-dashboard.php">Dashboard</a><a href="students.php">Manage Students</a><a href="../logout.php">Logout</a></nav>
</div></header>
<main>
<section class="page-header"><div class="container"><h1>Manage Students</h1><p>Admin-only student records: create, view, edit, search and delete.</p></div></section>
<div class="admin-wrap">
<?php if ($message !== ''): ?><div class="notice" role="status"><?= e_students($message) ?></div><?php endif; ?>
<div class="panel"><div class="toolbar">
<div><h2>Student Records</h2><p class="muted"><?= $total ?> record(s) found</p></div>
<div class="actions"><a class="btn btn-primary" href="student-create.php">+ Add Student</a><a class="btn-small" href="../admin-dashboard.php">Back to Dashboard</a></div>
</div>
<form class="search-form" method="get" action="students.php">
<input type="search" name="q" value="<?= e_students($search) ?>" placeholder="Search name, email or course" aria-label="Search students">
<button class="btn btn-primary" type="submit">Search</button>
<?php if ($search !== ''): ?><a class="btn-small" href="students.php">Clear</a><?php endif; ?>
</form>
</div>
<div class="panel table-wrap">
<table>
<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Course</th><th>Year</th><th>Role</th><th>Actions</th></tr></thead>
<tbody>
<?php if (!$students): ?><tr><td colspan="8">No student records found.</td></tr>
<?php else: foreach ($students as $student): ?>
<tr>
<td><?= (int)$student['student_id'] ?></td>
<td><?= e_students((string)$student['full_name']) ?></td>
<td><?= e_students((string)$student['email']) ?></td>
<td><?= e_students((string)($student['mobile'] ?? '—')) ?></td>
<td><?= e_students((string)$student['course']) ?></td>
<td><?= (int)$student['study_year'] ?></td>
<td><?= e_students((string)$student['role']) ?></td>
<td><div class="actions">
<a class="btn-small" href="student-edit.php?id=<?= (int)$student['student_id'] ?>">Edit</a>
<?php if ((int)$student['student_id'] !== (int)$_SESSION['student_id']): ?>
<form method="post" action="student-delete.php" onsubmit="return confirm('Delete this student record? This cannot be undone.');">
<input type="hidden" name="csrf_token" value="<?= e_students($_SESSION['students_csrf']) ?>">
<input type="hidden" name="student_id" value="<?= (int)$student['student_id'] ?>">
<button type="submit" class="btn-small danger">Delete</button>
</form>
<?php else: ?><span class="muted">Current account</span><?php endif; ?>
</div></td>
</tr>
<?php endforeach; endif; ?>
</tbody></table>
<div class="pagination">
<?php if ($page > 1): ?><a class="btn-small" href="?q=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Previous</a><?php endif; ?>
<span>Page <?= $page ?> of <?= $totalPages ?></span>
<?php if ($page < $totalPages): ?><a class="btn-small" href="?q=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next</a><?php endif; ?>
</div>
</div>
</div>
</main>
<footer class="site-footer"><div class="container"><p>&copy; <?= date('Y') ?> StudentHub</p></div></footer>
<script src="../js/script.js"></script>
</body></html>
