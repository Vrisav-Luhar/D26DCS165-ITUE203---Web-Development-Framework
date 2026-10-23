STUDENTHUB PRACTICAL 15 — DOCUMENTED FINAL PACKAGE

Folder: C:\xampp\htdocs\StudentHub-15

Documentation included:
- README.md — project overview and setup
- docs/PROJECT-REPORT.md — academic project report
- docs/USER-MANUAL.md — Admin/Student usage instructions
- docs/INSTALLATION-AND-DEPLOYMENT.md — XAMPP, SQL export, GitHub
- docs/TEST-REPORT.md — testing template with user-reported working status clearly distinguished from tests not separately confirmed
- docs/SECURITY-ACCESSIBILITY-CHECKLIST.md — review checklist
- docs/API-REFERENCE.md — student/event JSON API guide
- docs/PRACTICAL-MAPPING.md — practicals 1–15 mapping
- docs/VIVA-QUESTIONS.md — short viva questions and answers
- docs/FINAL-SUBMISSION-CHECKLIST.md — submission checklist

NEXT STEPS
1. Test all routes in your local XAMPP environment.
2. Fill docs/TEST-REPORT.md with actual results and evidence.
3. Capture screenshots into a screenshots/ directory.
4. Export and review your database SQL from phpMyAdmin; do not publish sensitive records.
5. Follow docs/INSTALLATION-AND-DEPLOYMENT.md to submit to GitHub.

Note: The user reports all pages are working. The test report avoids claiming that every security/accessibility scenario was independently verified.

Practical 15 final integration additions:
- Run database/practical15_final_migration.sql once in phpMyAdmin (database studenthub_db).
- Event posters: JPG, PNG, or WEBP up to 2 MB; files are stored in uploads/event-posters/.
- Admin audit history: open admin/audit-log.php after creating, editing, or deleting an event.
- Student-facing events and featured slider load from api/public-events.php, which reads the shared MySQL events table.
- Shared CSS was repaired and expanded for consistent responsive styling across public and Admin pages.

SCREENSHOT GUIDANCE FOR PRACTICAL 15
1. Home page: http://localhost/StudentHub-15/
2. Student event consistency: http://localhost/StudentHub-15/events.html (shows records from the same MySQL events table).
3. Valid poster upload: Admin login > Manage Events > Add Event; upload a JPG/PNG/WEBP up to 2 MB, save, and show the poster in Manage Events.
4. Invalid poster upload: choose a PDF or an image over 2 MB; capture the server validation message.
5. Audit log: Admin login > Manage Events > edit/save an event > Audit Log; capture the CREATE/UPDATE/DELETE row.
6. Admin CRUD: Admin Dashboard > Manage Students / Manage Events; show data changes and audit entries.

IMPORTANT SETUP
Before testing the new poster and audit features, run database/practical15_final_migration.sql once in phpMyAdmin with studenthub_db selected. Do not rerun the base studenthub.sql on an existing database unless you understand the sample inserts and current data.
