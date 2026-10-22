STUDENTHUB — PRACTICAL 14
Admin Dashboard with Role-Based Access Control and Dynamic Menu

SEPARATE FOLDER
Extract this ZIP into C:\xampp\htdocs\ so the project path is:
C:\xampp\htdocs\StudentHub-14
Earlier folders remain unchanged.

PRESERVED
- css/style.css
- js/script.js
- Practical 10 login/session/role infrastructure
- Practical 11 student CRUD
- Practical 12 event CRUD
- Practical 13 JSON APIs and Fetch demo

WHAT IS ADDED
- dashboard.php: role-aware dashboard for Admin and Student
- includes/dynamic_menu.php: navigation links selected according to the authenticated user's role
- Links to dashboard.php added to existing dashboards where possible

SETUP
1. Extract the ZIP into C:\xampp\htdocs\.
2. Start Apache and MySQL in XAMPP.
3. Confirm the existing studenthub_db database and role column are available.
4. Open http://localhost/StudentHub-14/login.php and log in with an existing Admin or Student account.
5. Open the Practical 14 dynamic dashboard:
   http://localhost/StudentHub-14/dashboard.php
6. Check the menu:
   - Admin should see Dashboard, Events, Profile, Manage Students, Manage Events, Fetch API Demo, Database Check and Logout.
   - Student should see Dashboard, Events, Profile and Logout only.
7. Try accessing admin/students.php, admin/events.php, api-demo.php and the API endpoints while logged in as Student. They should deny access through their existing role checks.

IMPORTANT
- This practical shares studenthub_db with previous project copies, so use test data.
- Existing role-based checks in admin CRUD/API files remain in place. Hiding menu links is not a security boundary by itself.
- This ZIP has not been run in the user's local XAMPP environment.
