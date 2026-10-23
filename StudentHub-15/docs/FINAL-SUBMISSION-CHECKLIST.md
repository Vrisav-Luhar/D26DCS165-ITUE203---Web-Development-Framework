# Final Submission Checklist

## Source code
- [ ] Final `StudentHub-15` folder opens in VS Code.
- [ ] `css/style.css` and `js/script.js` are included.
- [ ] PHP configuration matches the target environment.
- [ ] No unused secrets or credentials are present.
- [ ] Project works at the documented localhost URL.

## Documentation
- [ ] `README.md` included.
- [ ] Project report included.
- [ ] User manual included.
- [ ] Installation/deployment guide included.
- [ ] Test report completed with actual results.
- [ ] Security/accessibility checklist reviewed.
- [ ] API reference included.
- [ ] Practical mapping included.
- [ ] Viva questions reviewed.

## Evidence
- [ ] Home page screenshot.
- [ ] Registration validation screenshot.
- [ ] Login and role dashboard screenshots.
- [ ] Student CRUD screenshot.
- [ ] Event CRUD screenshot.
- [ ] Fetch API operation screenshot.
- [ ] Database/ER relationship evidence.
- [ ] Screenshots do not expose passwords or sensitive data.

## Database
- [ ] Database backup created.
- [ ] SQL export reviewed.
- [ ] Personal/sensitive data removed or anonymized.
- [ ] Required schema and migration files are documented.

## GitHub
- [ ] Repository created.
- [ ] `.gitignore` reviewed.
- [ ] No secrets or sensitive data committed.
- [ ] Source and docs committed.
- [ ] README setup instructions checked.
- [ ] Repository URL recorded for submission.

## Final review
- [ ] Admin authorization tested server-side.
- [ ] Student access to admin routes tested.
- [ ] Invalid form/API input tested.
- [ ] Database persistence tested after refresh.
- [ ] Responsive layout checked.
- [ ] Keyboard navigation and labels checked.
- [ ] Remaining limitations disclosed.

Practical 15 final integration additions:
- Run database/practical15_final_migration.sql once in phpMyAdmin (database studenthub_db).
- Event posters: JPG, PNG, or WEBP up to 2 MB; files are stored in uploads/event-posters/.
- Admin audit history: open admin/audit-log.php after creating, editing, or deleting an event.
- Student-facing events and featured slider load from api/public-events.php, which reads the shared MySQL events table.
- Shared CSS was repaired and expanded for consistent responsive styling across public and Admin pages.
