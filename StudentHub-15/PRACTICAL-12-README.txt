STUDENTHUB — PRACTICAL 12
Admin Event Management CRUD (Create, Read, Update, Delete)

SEPARATE FOLDER
Extract this ZIP into C:\xampp\htdocs\ so the folder is C:\xampp\htdocs\StudentHub-12.
StudentHub-11 remains unchanged.

CSS/JS
The package is copied from Practical 11 and retains the same css/style.css and js/script.js files. New admin event pages link to those same styles/scripts.

WHAT IS ADDED
- admin/events.php — list, search, pagination and registration counts
- admin/event-create.php — add event
- admin/event-edit.php — edit event
- admin/event-delete.php — delete event by POST
- Admin Dashboard button for Manage Events
- Role checks, CSRF tokens, prepared statements and server-side validation
- Prevents lowering event capacity below current active registrations
- Prevents deleting events that have registrations

SETUP
1. Extract the ZIP into C:\xampp\htdocs\ (folder name StudentHub-12).
2. Start Apache and MySQL in XAMPP.
3. Use the existing studenthub_db database from Practical 10/11. No new SQL migration is required.
4. Confirm your database has events(event_id, title, description, event_date, venue, capacity, created_at) and registrations(event_id, status).
5. Open http://localhost/StudentHub-12/login.php
6. Log in as Admin.
7. Open Admin Dashboard and click Manage Events (Practical 12), or visit:
   http://localhost/StudentHub-12/admin/events.php

TEST CASES
A. Admin opens Manage Events and sees current event records.
B. Create a new test event; it appears in the list.
C. Search by title, venue or description.
D. Edit event details; changes appear.
E. Try to reduce capacity below active registrations; update is rejected.
F. Try deleting an event with registrations; deletion is blocked.
G. Delete a test event with no registrations.
H. Log in as Student and visit admin/events.php; access should be denied.

DATABASE SHARING WARNING
StudentHub-12 uses the same local studenthub_db database as the earlier folders. Event changes are visible to other project copies using that database. Use test events, and do not delete important event records.

The package has not been run inside your local XAMPP installation; test locally.
