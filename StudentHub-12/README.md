# StudentHub


## Practical 12 fix: Student events use the database
The student-facing `events.html` now requests `api/events.php`, which reads the same MySQL `events` table used by the Admin event CRUD pages. The student event cards and featured slider are rendered from the database response, so static sample events from `data/events.json` are no longer shown on this page.

Run this project through XAMPP (Apache + MySQL), not VS Code Live Server, because PHP endpoints must execute on the server:
`http://localhost/StudentHub-12/events.html`

If your project folder has a different name, replace `StudentHub-12` in the URL. Check the `config/db.php` connection settings and import `database/studenthub.sql` if you have not already created the database.
