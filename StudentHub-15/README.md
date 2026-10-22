# StudentHub — Student Portal

**Course:** ITUE203 — Web Development Frameworks  
**Semester:** 3rd Semester, 2026–27  
**Project type:** Full-stack web application  
**Local development environment:** XAMPP (Apache, PHP, MySQL)

## 1. Project overview

StudentHub is a student portal prototype that combines a public-facing website with account authentication, role-aware dashboards, student and event administration, and a PHP JSON API used by a JavaScript Fetch interface.

The project was developed incrementally through Practical 1–15. The final folder is a consolidated code copy for integration, testing, documentation, and submission.

## 2. Objectives

- Build a semantic and responsive website using HTML and CSS.
- Add client-side interactions and form validation with JavaScript.
- Process selected forms using PHP and server-side validation.
- Store application records in MySQL using PDO.
- Implement registration, login, logout, session timeout, role checks, and remember-me token support.
- Implement student and event CRUD operations.
- Demonstrate JSON APIs and asynchronous CRUD using Fetch.
- Prepare installation instructions, test evidence, and a GitHub submission.

## 3. Technology stack

| Technology | Use |
|---|---|
| HTML5 | Page structure and forms |
| CSS3 | Layout, styling, responsive presentation |
| JavaScript | DOM interactions, client validation, Fetch requests |
| PHP | Server-side processing, authentication, page rendering, JSON API |
| MySQL | Persistent student, event, registration, and token data |
| PDO | Database access and prepared statements |
| XAMPP | Local Apache/PHP/MySQL environment |
| Git/GitHub | Version control and submission |

## 4. Main features

- Public pages: Home, About, Events, FAQ, Contact, Feedback, Login, and Registration.
- Account registration with duplicate-email validation and password hashing.
- Login/logout, role-aware access, inactivity timeout, and remember-me token support.
- Admin dashboard with student and event management.
- Student-facing dashboard/profile pages.
- Student CRUD and event CRUD with search.
- JSON API endpoints for student and event records.
- Fetch API demo for listing, searching, creating, editing, and deleting records asynchronously.
- Database connection check and final integration page.

## 5. Folder structure

```text
StudentHub-15/
├── index.html, about.html, faq.html, events.html
├── login.html, register.html, login.php, register.php, logout.php
├── admin-dashboard.php, student-dashboard.php, dashboard.php
├── final-integration.php, api-demo.php, db-check.php, db_test.php
├── admin/
│   ├── students.php
│   ├── student-create.php
│   ├── student-edit.php
│   ├── student-delete.php
│   ├── events.php
│   ├── event-create.php
│   ├── event-edit.php
│   └── event-delete.php
├── api/
│   ├── _common.php
│   ├── students.php
│   └── events.php
├── config/
│   ├── db.php
│   └── auth.php
├── includes/dynamic_menu.php
├── css/style.css
├── js/script.js
├── data/
├── process/
├── database/
└── docs/
```

## 6. Installation using XAMPP

1. Install XAMPP and start **Apache** and **MySQL**.
2. Extract this folder to `C:\xampp\htdocs\StudentHub-15`.
3. Open `config/db.php`. Confirm host, database name, username, and password match your local MySQL setup.
4. Open phpMyAdmin and select `studenthub_db`.
5. If creating a fresh database, first back up any existing database, then import `database/studenthub.sql`. The schema file creates the core `students`, `events`, and `registrations` tables and inserts demo records. Authentication may also require the migration in `database/practical10_migration.sql`; inspect it and apply only if it matches your current schema.
6. Do not repeatedly import setup SQL into a database containing real data without reviewing the statements and creating a backup.
7. Open `http://localhost/StudentHub-15/login.php`.
8. Sign in with an existing account, then open `http://localhost/StudentHub-15/final-integration.php`.

### Database configuration

Default XAMPP values in this project are commonly:

- Host: `localhost`
- Database: `studenthub_db`
- Username: `root`
- Password: blank

These are only defaults. Update `config/db.php` if your local MySQL setup uses different credentials.

## 7. Roles and access

The application uses the `role` value stored for the authenticated account. Admin pages should enforce authorization on the server; hiding an admin link in the menu is not sufficient security.

- **Admin:** access to student and event management, dashboard summaries, and API demo.
- **Student:** access to student-facing dashboard/profile and permitted public/student pages.

Use the application's existing admin account. Do not expose or commit admin setup credentials.

## 8. Main routes

| Route | Purpose |
|---|---|
| `index.html` | Public home page |
| `register.php` | Account registration processing |
| `login.php` | Login processing |
| `logout.php` | End the authenticated session |
| `admin-dashboard.php` | Admin dashboard |
| `student-dashboard.php` | Student dashboard |
| `dashboard.php` | Practical 14 role-aware dashboard |
| `admin/students.php` | Student management |
| `admin/events.php` | Event management |
| `api-demo.php` | Practical 13 Fetch CRUD interface |
| `api/students.php` | Student JSON API |
| `api/events.php` | Event JSON API |
| `db-check.php` | Admin database diagnostic |
| `final-integration.php` | Practical 15 integration landing page |

## 9. Testing and evidence

Use `docs/TEST-REPORT.md` to record the tests you actually perform. Save screenshots under a `screenshots/` directory and refer to their filenames in the report. A user-reported working page is not a substitute for documenting the specific test case and evidence.

## 10. Security notes

- Use `password_hash()` and `password_verify()` for passwords.
- Use PDO prepared statements for database input.
- Validate inputs on the server even when browser-side validation exists.
- Escape dynamic output before placing it in HTML.
- Keep role checks on protected server routes.
- Use HTTPS and secure configuration for public deployment.
- Do not publish database dumps containing real student information.
- Restrict or remove diagnostic pages such as `db-check.php` and `db_test.php` before production deployment.
- Review all PHP endpoints, uploads, CSRF handling, and session behavior before deploying publicly.

## 11. GitHub submission

From a terminal opened in the project directory:

```bash
git init
git add .
git commit -m "Complete StudentHub WDF Practical 15"
git branch -M main
git remote add origin YOUR_GITHUB_REPOSITORY_URL
git push -u origin main
```

Create an empty repository on GitHub and replace `YOUR_GITHUB_REPOSITORY_URL` with the URL GitHub gives you. If the repository already exists, inspect its remote and branch before adding a new one.

Before pushing:
- Review `.gitignore`.
- Remove secrets, credentials, tokens, and personal data.
- Review any SQL export.
- Add project screenshots and final documentation.
- Confirm the repository README has clear setup instructions.

## 12. Deployment scope

This package is intended for local XAMPP/Apache use. It has not been independently executed in this documentation environment. Before any public deployment, configure a supported PHP/MySQL host, HTTPS, production credentials, restricted error reporting, backups, upload limits, and server-side security controls.

## 13. Limitations

This is an academic prototype. Some pages may be demonstration or legacy pages from earlier practicals. Verify each route in the final folder and document any unsupported or incomplete behavior rather than claiming it was tested.
