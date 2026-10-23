# Installation and Local Deployment Guide

## Requirements
- Windows (for the paths shown here)
- XAMPP with Apache, PHP and MySQL/MariaDB
- Modern browser
- VS Code or another editor

## Install locally
1. Extract the project folder as `C:\xampp\htdocs\StudentHub-15`.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Open `config/db.php` and set the correct database name and credentials.
4. Open `http://localhost/phpmyadmin`.
5. Back up your current database before running migrations or importing SQL.
6. For a fresh database, review and import `database/studenthub.sql`. Review `database/practical10_migration.sql` separately for authentication-related changes.
7. Open `http://localhost/StudentHub-15/login.php`.
8. Sign in using an existing account and test the role-appropriate dashboard.
9. Open `http://localhost/StudentHub-15/final-integration.php`.

## Local virtual host (optional)
Virtual-host setup depends on the installed XAMPP version and Apache configuration. Make backups before editing Apache configuration. Configure a local host name in Apache's virtual-host configuration and map it to the project's document root; add the same local hostname to the Windows hosts file. Restart Apache and test the hostname. Do not expose a development virtual host to the public internet.

## Database export for submission
1. Open phpMyAdmin and select `studenthub_db`.
2. Choose **Export**.
3. Choose **Custom** if you need to select tables, or **Quick** for a straightforward SQL export.
4. Choose SQL format and export.
5. Review the resulting file before placing it in `database/`.
6. Remove or anonymize personal data and never include secrets.
7. Keep a private backup separate from the GitHub repository.

## GitHub
1. Create a new repository on GitHub.
2. Open a terminal in the project directory.
3. Review files and remove secrets/test data that should not be public.
4. Run:

```bash
git init
git add .
git commit -m "Complete StudentHub Practical 15"
git branch -M main
git remote add origin YOUR_GITHUB_REPOSITORY_URL
git push -u origin main
```

5. Replace the placeholder URL with the repository URL from GitHub.
6. Confirm the repository contains README, docs, source files, and any approved screenshots.
7. Do not upload credentials or sensitive SQL exports.

## Public hosting warning
This local XAMPP project is not automatically ready for public hosting. Public deployment requires HTTPS, production-grade credentials, restricted error display, backups, correct file permissions, secure upload handling, and testing on the target PHP/MySQL host.

Practical 15 final integration additions:
- Run database/practical15_final_migration.sql once in phpMyAdmin (database studenthub_db).
- Event posters: JPG, PNG, or WEBP up to 2 MB; files are stored in uploads/event-posters/.
- Admin audit history: open admin/audit-log.php after creating, editing, or deleting an event.
- Student-facing events and featured slider load from api/public-events.php, which reads the shared MySQL events table.
- Shared CSS was repaired and expanded for consistent responsive styling across public and Admin pages.
