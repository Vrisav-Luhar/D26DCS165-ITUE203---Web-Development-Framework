STUDENTHUB — PRACTICAL 10
Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token

PRACTICAL 9 STATUS
Practical 9 registration is retained. Registration inserts users into students and stores passwords using password_hash().
All existing users become role='student' when the Practical 10 migration runs.

SETUP
1. Extract the ZIP into C:\xampp\htdocs\ so the folder is C:\xampp\htdocs\StudentHub-10.
2. Start Apache and MySQL in XAMPP.
3. Confirm studenthub_db exists (Practical 8/9 database).
4. In phpMyAdmin select studenthub_db > Import and import database/practical10_migration.sql.
   IMPORTANT: The ALTER TABLE adding role is intended to be run ONCE. If role already exists, remove/comment out that ALTER TABLE statement before importing.
5. Check config/db.php credentials if your local MySQL uses a password.
6. Create an admin account by opening http://localhost/StudentHub-10/setup_admin.php
   - Use a test/admin email and a strong unique password.
   - Once created, DELETE setup_admin.php from the folder immediately.
7. Open http://localhost/StudentHub-10/login.php
8. Register a normal student at http://localhost/StudentHub-10/register.php if needed.

WHAT IS IMPLEMENTED
- Login checks password with password_verify()
- Prepared SQL statement for user lookup
- session_regenerate_id(true) after successful login
- Role-based redirection to admin-dashboard.php or student-dashboard.php
- studenthub_require_role() blocks unauthorized dashboard access with HTTP 403
- Logout clears session and remember-me token/cookie
- Session timeout after 15 minutes inactivity
- Optional remember-me cookie for 30 days with random selector/validator; only validator hash stored in database
- CSRF token on login form
- Generic invalid-credentials message

TEST CASES
A. Valid student login -> student-dashboard.php
B. Wrong password -> "Invalid email or password"
C. Admin login -> admin-dashboard.php
D. Student directly opens admin-dashboard.php -> HTTP 403
E. Logout -> redirected to login.php and dashboard access blocked
F. Session timeout -> wait 15 minutes inactive, then request a protected page; should redirect to login.php?timeout=1
G. Remember me -> tick on login, close/reopen browser on same device before token expiry, visit login.php; it should restore the session
H. Check remember_me_tokens in phpMyAdmin; only validator_hash is stored, not the raw validator.

NOTES
- Remember-me tokens are optional in the practical brief but included here.
- Cookies are HttpOnly and SameSite=Lax; Secure is enabled automatically on HTTPS.
- A remember-me cookie is a bearer credential. Do not use it on shared/public computers.
- Local XAMPP HTTP is suitable for coursework testing only, not public deployment.
- setup_admin.php is a one-time bootstrap utility. DELETE it after creating an administrator.
- This ZIP has not been run in your local XAMPP environment. Verify each test locally.


FOREIGN KEY FIX
The Practical 8/9 schema defines students.student_id as INT UNSIGNED.
The remember_me_tokens.student_id column is therefore INT UNSIGNED too.
If an earlier attempt failed to create remember_me_tokens, this corrected migration can be run after checking:
- The role column has not already been added (otherwise comment out the ALTER TABLE statement).
- The remember_me_tokens table was not partially created (CREATE TABLE IF NOT EXISTS is safe if it exists).
If you already successfully added role, do not run the ALTER TABLE ADD COLUMN role statement again.
