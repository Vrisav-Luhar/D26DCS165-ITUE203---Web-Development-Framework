<?php
declare(strict_types=1);

/**
 * Practical 14 audit helper. Include after config/auth.php.
 * The audit table is created by database/practical14_audit_log.sql.
 */
function studenthub_audit(PDO $pdo, string $action, string $entityType, ?int $entityId = null, string $details = ''): void
{
    $actorId = isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : null;
    $role = (string)($_SESSION['role'] ?? 'unknown');
    $email = (string)($_SESSION['student_email'] ?? '');
    $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);

    $stmt = $pdo->prepare(
        'INSERT INTO audit_logs (actor_student_id, actor_email, actor_role, action, entity_type, entity_id, details, ip_address)
         VALUES (:actor_id, :email, :role, :action, :entity_type, :entity_id, :details, :ip)'
    );
    $stmt->execute([
        'actor_id' => $actorId,
        'email' => $email !== '' ? $email : null,
        'role' => $role,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'details' => mb_substr($details, 0, 1000),
        'ip' => $ip !== '' ? $ip : null,
    ]);
}
