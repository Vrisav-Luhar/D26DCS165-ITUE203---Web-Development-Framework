STUDENTHUB — PRACTICAL 11
Admin Student CRUD (Create, Read, Update, Delete)

FOLDER
Extract this ZIP to C:\xampp\htdocs\ so the folder is C:\xampp\htdocs\StudentHub-11.
This is a separate copy; StudentHub-10 remains unchanged.

WHAT PRACTICAL 11 ADDS
- Admin-only student management page: admin/students.php
- Add student: admin/student-create.php
- Edit student: admin/student-edit.php
- Delete student via POST: admin/student-delete.php
- Search by name, email or course
- Pagination
- PDO prepared statements and server-side validation
- Duplicate-email checks and password hashing for newly created student accounts
- CSRF tokens for state-changing forms
- Current admin cannot delete their own account
- Reuses Practical 10 login/session/role checks and Practical 8/9 database schema

SETUP
1. Extract the ZIP into C:\xampp\htdocs\ (folder name StudentHub-11).
2. Start Apache and MySQL in XAMPP.
3. Confirm studenthub_db is the same database you used in Practical 10.
4. No new SQL migration is required for Practical 11.
5. If your database is in good state and Practical 10 login works, open:
   http://localhost/StudentHub-11/login.php
6. Log in as Admin, then open:
   http://localhost/StudentHub-11/admin/students.php
   Or use the Manage Students button on the Admin Dashboard.

IMPORTANT DATABASE NOTE
Practical 11 assumes the Practical 10 `role` column is present and that your Admin account has role='admin'.
If you are testing this separate folder against the same local studenthub_db, it shares the same data as StudentHub-10. CRUD changes affect the shared database. Use test records, and do not delete real accounts.

TEST CASES
A. Admin opens Manage Students -> records list.
B. Add a new test student -> record appears in list and students table.
C. Try adding same email -> duplicate email error.
D. Edit test student's course/year/name -> changes are saved.
E. Search by name/email/course -> matching records appear.
F. Delete a test student -> record is removed.
G. Log in as Student and visit admin/students.php -> access denied/redirect to login.
H. Try to delete the currently logged-in Admin -> blocked.

NOTES
- This ZIP has not been run inside your local XAMPP installation.
- Admin can assign the Admin role to another account from Edit Student. Only do so for trusted users.
- Deleting a student may be blocked by database foreign keys if they have registrations or remember-me tokens; the page reports a safe error rather than cascading unrelated records.
