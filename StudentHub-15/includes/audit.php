<?php
declare(strict_types=1);

/** Record an administrator action. Call only after the action succeeds. */
function studenthub_audit(PDO $pdo, string $action, string $entityType, ?int $entityId, string $details): void {
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO audit_logs (actor_id, actor_email, actor_role, action, entity_type, entity_id, details, ip_address)
             VALUES (:actor_id, :actor_email, :actor_role, :action, :entity_type, :entity_id, :details, :ip_address)'
        );
        $stmt->execute([
            'actor_id' => isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : null,
            'actor_email' => substr((string)($_SESSION['student_email'] ?? 'unknown'), 0, 190),
            'actor_role' => substr((string)($_SESSION['role'] ?? 'unknown'), 0, 30),
            'action' => strtoupper(substr($action, 0, 30)),
            'entity_type' => substr($entityType, 0, 50),
            'entity_id' => $entityId,
            'details' => substr($details, 0, 1000),
            'ip_address' => substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
        ]);
    } catch (Throwable $e) {
        // Never fail the main action because the audit write failed; log the issue server-side.
        error_log('StudentHub audit logging failed: ' . $e->getMessage());
    }
}
