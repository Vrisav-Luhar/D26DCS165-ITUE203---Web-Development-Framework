<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
studenthub_require_role($pdo, 'admin');

$actionFilter = trim((string)($_GET['action'] ?? ''));
$allowedActions = ['CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'ROLE_CHANGE'];
$where = '';
$params = [];
if (in_array($actionFilter, $allowedActions, true)) {
    $where = ' WHERE action = :action';
    $params['action'] = $actionFilter;
}
$stmt = $pdo->prepare(
    'SELECT audit_id, actor_student_id, actor_email, actor_role, action, entity_type,
            entity_id, details, ip_address, created_at
     FROM audit_logs' . $where . ' ORDER BY created_at DESC, audit_id DESC LIMIT 200'
);
$stmt->execute($params);
$logs = $stmt->fetchAll();
function e_audit(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Audit Log - StudentHub</title><link rel="stylesheet" href="../css/style.css">
<style>
.audit-wrap{max-width:1250px;margin:2rem auto;padding:0 1rem}.panel{background:var(--card-bg,#fff);border:1px solid #ddd;border-radius:12px;padding:1.25rem;margin:1rem 0}
.table-wrap{overflow-x:auto}table{border-collapse:collapse;width:100%;min-width:900px}th,td{text-align:left;padding:.7rem;border-bottom:1px solid #ddd;vertical-align:top}th{background:#f2f5fa}
.pill{display:inline-block;padding:.2rem .5rem;border-radius:6px;background:#edf2ff;font-weight:700;font-size:.85rem}
.filter{display:flex;gap:.7rem;align-items:center;flex-wrap:wrap}.filter select{padding:.65rem;border:1px solid #bbb;border-radius:8px}
</style></head>
<body>
<header class="site-header"><div class="container nav-container"><a href="../index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="../dashboard.php">Dashboard</a><a href="events.php">Manage Events</a><a href="../logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Audit Log</h1><p>Admin activity history for Practical 14.</p></div></section>
<div class="audit-wrap">
<div class="panel"><form class="filter" method="get"><label for="action">Filter by action</label><select name="action" id="action"><option value="">All actions</option><?php foreach($allowedActions as $a): ?><option value="<?=e_audit($a)?>" <?=$actionFilter===$a?'selected':''?>><?=e_audit($a)?></option><?php endforeach;?></select><button class="btn btn-primary" type="submit">Filter</button><a class="btn-small" href="audit-log.php">Clear</a></form><p><?=count($logs)?> log entr<?=count($logs)===1?'y':'ies'?> shown (latest 200 maximum).</p></div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>ID</th><th>Date / Time</th><th>Actor</th><th>Role</th><th>Action</th><th>Entity</th><th>Details</th><th>IP Address</th></tr></thead><tbody>
<?php if (!$logs): ?><tr><td colspan="8">No audit entries yet. Perform an Admin action, such as editing an event, and return here.</td></tr><?php else: foreach($logs as $row): ?><tr><td><?= (int)$row['audit_id'] ?></td><td><?=e_audit((string)$row['created_at'])?></td><td><?=e_audit((string)($row['actor_email'] ?: ('User #'.($row['actor_student_id'] ?? 'unknown'))))?></td><td><?=e_audit((string)$row['actor_role'])?></td><td><span class="pill"><?=e_audit((string)$row['action'])?></span></td><td><?=e_audit((string)$row['entity_type'])?><?= $row['entity_id'] !== null ? ' #'.(int)$row['entity_id'] : '' ?></td><td><?=e_audit((string)$row['details'])?></td><td><?=e_audit((string)($row['ip_address'] ?? ''))?></td></tr><?php endforeach; endif; ?>
</tbody></table></div></div>
<p><a class="btn-small" href="events.php">Back to Manage Events</a> <a class="btn-small" href="../admin-dashboard.php">Back to Admin Dashboard</a></p>
</div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer>
</body></html>
