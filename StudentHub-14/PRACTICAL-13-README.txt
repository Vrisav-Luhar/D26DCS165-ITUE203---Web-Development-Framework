STUDENTHUB — PRACTICAL 13
PHP JSON API + AJAX / Fetch API CRUD

SEPARATE FOLDER
Extract this ZIP into C:\xampp\htdocs\ so the folder is C:\xampp\htdocs\StudentHub-13.
StudentHub-12 remains unchanged.

CSS/JS
The folder is copied from Practical 12 and retains css/style.css and js/script.js. The new api-demo.php uses the existing stylesheet and script.

WHAT IS ADDED
- api/students.php: JSON API supporting GET, POST, PUT, DELETE
- api/events.php: JSON API supporting GET, POST, PUT, DELETE
- api/_common.php: shared JSON response, method, CSRF and Admin checks
- api-demo.php: Fetch API interface to view/search/add/edit/delete students and events without full page reloads
- Uses PDO prepared statements, server-side validation, JSON responses, role checks and CSRF header on write requests

SETUP
1. Extract to C:\xampp\htdocs\StudentHub-13.
2. Start Apache and MySQL in XAMPP.
3. Confirm studenthub_db is the same database used for Practical 10–12, and the role column exists.
4. Open http://localhost/StudentHub-13/login.php and log in as Admin.
5. Open the Admin Dashboard and click “AJAX / Fetch CRUD (Practical 13)”, or visit:
   http://localhost/StudentHub-13/api-demo.php
6. Use search, add student, edit/delete student, add event, edit/delete event. Requests should update the tables without reloading the whole page.

TEST CASES
A. Admin loads API demo and sees student/event data.
B. Search for a student or event.
C. Create a test student and verify it appears without a page reload.
D. Edit the test student, then delete it.
E. Create a test event and verify it appears without a page reload.
F. Edit the test event; try deleting an event with registrations (should be blocked).
G. Log in as Student and try api-demo.php or api/students.php; access should be denied.
H. Invalid input should return JSON error messages.
I. If a write request is sent without the CSRF token, API returns 403.

DATABASE SHARING WARNING
StudentHub-13 shares studenthub_db with previous project copies. Changes affect the same database. Use test records; do not delete important data.

NOTES
- API responses are JSON. For the browser's direct URL to api/students.php, you must be logged in as Admin; JSON will be displayed.
- Editing uses prompt dialogs to keep the demo compact. The regular CRUD pages from Practical 11/12 offer full forms.
- This package has not been run inside your local XAMPP environment. Verify it locally.
