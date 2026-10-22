CREATE DATABASE IF NOT EXISTS studenthub_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE studenthub_db;

CREATE TABLE IF NOT EXISTS students (
    student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(15) DEFAULT NULL,
    course VARCHAR(100) DEFAULT NULL,
    study_year TINYINT UNSIGNED DEFAULT NULL,
    password_hash VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_student_year
        CHECK (study_year IS NULL OR study_year BETWEEN 1 AND 6)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
    event_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    event_date DATETIME NOT NULL,
    venue VARCHAR(150) NOT NULL,
    capacity INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    event_id INT UNSIGNED NOT NULL,
    registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('registered', 'cancelled', 'attended')
        NOT NULL DEFAULT 'registered',
    CONSTRAINT fk_registration_student
        FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_registration_event
        FOREIGN KEY (event_id) REFERENCES events(event_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT uq_student_event UNIQUE (student_id, event_id),
    INDEX idx_registration_event (event_id),
    INDEX idx_registration_student (student_id)
) ENGINE=InnoDB;

INSERT IGNORE INTO students
    (full_name, email, mobile, course, study_year)
VALUES
    ('Aarav Patel', 'aarav.demo@example.com', '9876543210', 'B.Tech Computer Science', 2),
    ('Diya Shah', 'diya.demo@example.com', '9876543211', 'B.Tech Information Technology', 3),
    ('Kabir Mehta', 'kabir.demo@example.com', '9876543212', 'B.Tech Computer Science', 1);

INSERT INTO events (title, description, event_date, venue, capacity)
SELECT 'Web Development Workshop',
       'A practical workshop on HTML, CSS, JavaScript and PHP.',
       '2027-01-15 10:00:00', 'Computer Lab 1', 60
WHERE NOT EXISTS (
    SELECT 1 FROM events
    WHERE title = 'Web Development Workshop'
      AND event_date = '2027-01-15 10:00:00'
);

INSERT INTO events (title, description, event_date, venue, capacity)
SELECT 'AI and Machine Learning Seminar',
       'An introductory seminar on AI and machine learning.',
       '2027-01-22 11:00:00', 'Seminar Hall', 100
WHERE NOT EXISTS (
    SELECT 1 FROM events
    WHERE title = 'AI and Machine Learning Seminar'
      AND event_date = '2027-01-22 11:00:00'
);

INSERT IGNORE INTO registrations (student_id, event_id, status)
SELECT s.student_id, e.event_id, 'registered'
FROM students AS s
CROSS JOIN events AS e
WHERE s.email = 'aarav.demo@example.com'
  AND e.title = 'Web Development Workshop'
  AND e.event_date = '2027-01-15 10:00:00';

-- Verify the imported tables:
SELECT * FROM students;
SELECT * FROM events;
SELECT * FROM registrations;
