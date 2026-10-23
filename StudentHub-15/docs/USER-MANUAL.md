# StudentHub User Manual

## 1. Open the website

1. Start Apache and MySQL from XAMPP.
2. Open `http://localhost/StudentHub-15/` in a browser.
3. Use the public navigation links to explore available information pages.

## 2. Register an account

1. Open the registration page.
2. Complete all required fields.
3. Submit the form.
4. If validation fails, correct the displayed errors.
5. If the email is already registered, use the existing account rather than creating a duplicate.

Use fictional test information during demonstrations.

## 3. Log in and log out

1. Open `login.php`.
2. Enter the account email and password.
3. After successful login, use the dashboard and menu available to your role.
4. Choose Logout to end the session.
5. Protected pages should redirect to login or deny access after logout.

## 4. Admin workflow

1. Log in with an account whose database role is `admin`.
2. Open the Admin Dashboard.
3. Select Manage Students to list/search student records.
4. Use the create/edit/delete controls to demonstrate student CRUD.
5. Select Manage Events to manage event records.
6. Open the Fetch API Demo to try asynchronous operations.
7. Use the database diagnostic only during local development.

Do not delete important records during a live demonstration. Create clearly named test records and remove them only when safe.

## 5. Student workflow

1. Log in with an account whose role is `student`.
2. Open the Student Dashboard and Profile.
3. Use permitted links such as events or profile pages.
4. Admin-only pages should deny access even if their URLs are entered manually.

## 6. Fetch API demo

1. Log in as Admin.
2. Open `api-demo.php`.
3. Wait for student and event tables to load.
4. Search for a record.
5. Create a test student/event and verify that the table updates without a full-page reload.
6. Edit a test record.
7. Delete only test records. An event with registrations may be protected from deletion.
8. If a request fails, record the visible message and inspect the browser console and Apache/PHP error log.

## 7. Troubleshooting

### Database connection error
- Confirm MySQL is running.
- Check `config/db.php`.
- Confirm the database name in phpMyAdmin.
- Confirm the required tables and migration are present.

### Login redirects or access denied
- Confirm the correct project URL is used (`StudentHub-15`).
- Confirm the account role is correct.
- Log out and log in again to refresh the session.
- Do not change role values in a production database casually.

### Fetch page remains on Loading
- Open browser Developer Tools → Console and Network.
- Confirm the request URL starts with `/StudentHub-15/`.
- Check whether the response is JSON or an HTML login/error page.
- Confirm you are logged in as Admin.
- Check PHP/Apache logs for the underlying error.

### A record appears in one folder but not another
- Different project folders may use the same `studenthub_db`.
- Confirm both folders use the same database in `config/db.php`.
- Refresh the list page and check the database directly.

## 8. Safe demo practices
- Use test data only.
- Do not share passwords or remember-me cookies.
- Do not expose database diagnostic pages publicly.
- Back up the database before bulk changes.
