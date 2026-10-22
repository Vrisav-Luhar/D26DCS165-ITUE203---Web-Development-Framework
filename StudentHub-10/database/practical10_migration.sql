-- StudentHub Practical 10 migration
-- Run this ONCE against the existing studenthub_db database.
USE studenthub_db;

-- If the role column already exists, comment out the ALTER TABLE statement below.
/*ALTER TABLE students
    ADD COLUMN role ENUM('admin', 'student') NOT NULL DEFAULT 'student' AFTER password_hash;*/

-- IMPORTANT: students.student_id is INT UNSIGNED in the Practical 8/9 schema,
-- so this child foreign-key column must also be INT UNSIGNED.
-- Both tables use InnoDB so the foreign key can be created.
ALTER TABLE students ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS remember_me_tokens (
    token_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    selector CHAR(24) NOT NULL UNIQUE,
    validator_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_remember_student
        FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    INDEX idx_remember_expires (expires_at)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- All existing users become students by default.
UPDATE students SET role = 'student' WHERE role IS NULL OR role = '';
