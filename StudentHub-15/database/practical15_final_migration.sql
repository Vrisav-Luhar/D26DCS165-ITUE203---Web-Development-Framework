-- StudentHub Practical 15 final integration migration
-- Run once in phpMyAdmin after selecting studenthub_db.
USE studenthub_db;

-- Add poster_path only when it is not already present.
SET @poster_column_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'poster_path'
);
SET @poster_sql := IF(@poster_column_exists = 0,
  'ALTER TABLE events ADD COLUMN poster_path VARCHAR(255) NULL AFTER capacity',
  'SELECT "poster_path already exists" AS migration_note');
PREPARE poster_stmt FROM @poster_sql;
EXECUTE poster_stmt;
DEALLOCATE PREPARE poster_stmt;

CREATE TABLE IF NOT EXISTS audit_logs (
  audit_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  actor_id INT UNSIGNED NULL,
  actor_email VARCHAR(190) NOT NULL DEFAULT 'unknown',
  actor_role VARCHAR(30) NOT NULL DEFAULT 'unknown',
  action VARCHAR(30) NOT NULL,
  entity_type VARCHAR(50) NOT NULL,
  entity_id INT NULL,
  details VARCHAR(1000) NOT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_created_at (created_at),
  INDEX idx_audit_actor (actor_id),
  INDEX idx_audit_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
