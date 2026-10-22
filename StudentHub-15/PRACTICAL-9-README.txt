STUDENTHUB — PRACTICAL 9
Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing

WHAT THIS FOLDER ADDS
- register.php: server-side registration form and processing
- Uses the Practical 8 PDO connection in config/db.php
- Validates form data on the server
- Checks duplicate email using a prepared statement
- Hashes passwords with password_hash()
- Inserts the student using a prepared statement
- Uses a CSRF token as an additional safeguard
- Keeps the Practical 6 CSS/JS and Practical 7 Contact/Feedback files

SETUP (XAMPP / WINDOWS)
1. Extract this ZIP into C:\xampp\htdocs\
2. Confirm the folder name is StudentHub-9
3. Start Apache and MySQL in XAMPP Control Panel
4. Open phpMyAdmin: http://localhost/phpmyadmin
5. Import StudentHub-9/database/studenthub.sql (only if studenthub_db has not already been created)
   - If you have already imported this SQL for Practical 8, do not needlessly re-import it.
6. Check database credentials in StudentHub-9/config/db.php.
7. Open: http://localhost/StudentHub-9/register.php

TEST CASES
A. Valid registration:
   Use a new email address and a password like DemoPass@123 (do not use a real personal password).
   Expected: success message and record inserted in students table.
B. Duplicate email:
   Submit the same email again.
   Expected: duplicate-email message and no second record.
C. Invalid email:
   Enter an invalid email.
   Expected: browser and/or server-side validation message.
D. Weak password:
   Use a password that lacks required character types.
   Expected: server-side validation error.
E. Password mismatch:
   Enter different values in Password and Confirm Password.
   Expected: server-side validation error.
F. Database check:
   In phpMyAdmin, open studenthub_db > students > Browse.
   Confirm the new record exists and password_hash contains a hash, not the plain-text password.

IMPORTANT
- This Practical 9 copy is separate from StudentHub-8; it does not overwrite Practical 8.
- The registration form is register.php. The original register.html remains as the Practical 5 frontend-only form.
- Do not publish db_test.php or detailed error output on a public production server.
- This package has not been run inside your local XAMPP installation; verify using the steps above.
