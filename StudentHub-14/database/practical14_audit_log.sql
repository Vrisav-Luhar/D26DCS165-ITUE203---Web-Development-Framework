-- StudentHub Practical 14: audit log table
-- Run this SQL once in phpMyAdmin with studenthub_db selected.
USE studenthub_db;

CREATE TABLE IF NOT EXISTS audit_logs (
    audit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_student_id INT UNSIGNED NULL,
    actor_email VARCHAR(150) NULL,
    actor_role VARCHAR(30) NOT NULL DEFAULT 'unknown',
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT NULL,
    details VARCHAR(1000) NOT NULL DEFAULT '',
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_created_at (created_at),
    INDEX idx_audit_actor (actor_student_id),
    INDEX idx_audit_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
