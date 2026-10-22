STUDENTHUB - PRACTICAL 8
========================

This folder is a separate copy of your Practical 7 StudentHub project with
the Practical 8 MySQL/PDO files added. The original Practical 6 CSS/JS and
Practical 7 Contact/Feedback PHP and CSV files are retained.

FOLDER LOCATION
---------------
Extract this folder to:
C:\xampp\htdocs\StudentHub-8

SETUP
-----
1. Start Apache and MySQL in XAMPP Control Panel.
2. Open http://localhost/phpmyadmin/
3. Click Import and select:
   C:\xampp\htdocs\StudentHub-8\database\studenthub.sql
4. Click Go/Import.
5. Open config\db.php and adjust the username/password if your MySQL setup
   differs from the default XAMPP configuration.
6. Open:
   http://localhost/StudentHub-8/db_test.php

EXPECTED RESULT
---------------
The test page should show a successful database connection, sample students,
sample events, and registration counts.

PRACTICAL 7 PRESERVATION
------------------------
contact.php, feedback.php, process/contact_process.php,
process/feedback_process.php, data/contacts.csv, data/feedback.csv,
css/style.css, and js/script.js are included from StudentHub-7-Updated.
No existing stylesheet or JavaScript was intentionally modified.

IMPORTANT
---------
This is a local educational project. db_test.php is a development test page;
remove or restrict it before any public deployment. Never commit real database
passwords to a public repository.
