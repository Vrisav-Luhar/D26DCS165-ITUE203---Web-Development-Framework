-- StudentHub Practical 12: add optional event poster path. Run once in phpMyAdmin > SQL.
USE studenthub_db;
ALTER TABLE events ADD COLUMN poster_path VARCHAR(255) NULL DEFAULT NULL;
