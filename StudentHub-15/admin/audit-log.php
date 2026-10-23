<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';
studenthub_require_role($pdo, 'admin');
function e_audit(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
$action = strtoupper(trim((string)($_GET['action'] ?? '')));
$allowed = ['CREATE','UPDATE','DELETE','LOGIN','LOGOUT'];
if (!in_array($action, $allowed, true)) $action = '';
$where = $action !== '' ? ' WHERE action = :action' : '';
$sql = 'SELECT audit_id, created_at, actor_email, actor_role, action, entity_type, entity_id, details, ip_address FROM audit_logs' . $where . ' ORDER BY audit_id DESC LIMIT 200';
$stmt = $pdo->prepare($sql);
if ($action !== '') $stmt->execute(['action'=>$action]); else $stmt->execute();
$logs = $stmt->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Audit Log - StudentHub</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="site-header"><div class="container nav-container"><a href="../index.html" class="logo">Student<span>Hub</span></a><nav class="main-nav"><a href="../admin-dashboard.php">Dashboard</a><a href="students.php">Manage Students</a><a href="events.php">Manage Events</a><a href="../logout.php">Logout</a></nav></div></header>
<main><section class="page-header"><div class="container"><h1>Audit Log</h1><p>Admin activity history for the final integrated StudentHub project.</p></div></section><div class="admin-wrap">
<div class="panel"><form method="get" class="search-form"><label for="action">Filter by action</label><select id="action" name="action"><option value="">All actions</option><?php foreach($allowed as $a): ?><option value="<?=e_audit($a)?>" <?=$action===$a?'selected':''?>><?=e_audit($a)?></option><?php endforeach;?></select><button class="btn btn-primary" type="submit">Filter</button><a class="btn-small" href="audit-log.php">Clear</a></form><p class="muted"><?=count($logs)?> log entr<?=count($logs)===1?'y':'ies'?> shown (latest 200 maximum).</p></div>
<div class="panel table-wrap"><table><thead><tr><th>ID</th><th>Date / Time</th><th>Actor</th><th>Role</th><th>Action</th><th>Entity</th><th>Details</th><th>IP Address</th></tr></thead><tbody>
<?php if (!$logs): ?><tr><td colspan="8">No audit entries yet. Create, edit, or delete an event to generate an entry.</td></tr><?php else: foreach($logs as $log): $actionClass=strtolower((string)$log['action']); ?><tr><td><?= (int)$log['audit_id'] ?></td><td><?=e_audit((string)$log['created_at'])?></td><td><?=e_audit((string)$log['actor_email'])?></td><td><?=e_audit((string)$log['actor_role'])?></td><td><span class="audit-action <?=e_audit($actionClass)?>"><?=e_audit((string)$log['action'])?></span></td><td><?=e_audit((string)$log['entity_type'])?> #<?=e_audit((string)($log['entity_id']??''))?></td><td><?=e_audit((string)$log['details'])?></td><td><?=e_audit((string)($log['ip_address']??''))?></td></tr><?php endforeach; endif; ?></tbody></table></div>
<div class="actions"><a class="btn-small" href="events.php">Back to Manage Events</a><a class="btn-small" href="../admin-dashboard.php">Back to Admin Dashboard</a></div></div></main><footer class="site-footer"><div class="container"><p>&copy; <?=date('Y')?> StudentHub</p></div></footer></body></html>
