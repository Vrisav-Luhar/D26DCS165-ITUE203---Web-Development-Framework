# StudentHub Project Report

## Title
**StudentHub: Student Portal Web Application**

## 1. Abstract

StudentHub is a full-stack student portal prototype developed as part of the ITUE203 Web Development Frameworks practical series. It demonstrates the integration of HTML, CSS, JavaScript, PHP, MySQL, and Fetch API. The application includes public information pages, account registration and authentication, role-aware dashboards, student/event management, and JSON API endpoints.

## 2. Problem statement

Student information and campus event information often need a centralized web interface. A portal can provide a common place to present information and demonstrate secure, database-backed operations. StudentHub models this workflow for educational purposes.

## 3. Objectives

1. Design structured and responsive website pages.
2. Add accessible forms and JavaScript interactions.
3. Process forms on the server using PHP.
4. Store and retrieve records using MySQL and PDO.
5. Demonstrate password hashing and authentication sessions.
6. Restrict administrative functionality by role.
7. Implement CRUD operations for students and events.
8. Implement JSON endpoints and asynchronous Fetch API requests.
9. Integrate the modules and prepare testing/documentation for submission.

## 4. Scope

The project includes public pages, contact/feedback processing, registration/login/logout, student/event management, role-aware dashboards, and a Fetch API demonstration. It is intended as a learning prototype, not as a production-ready institutional information system.

## 5. Architecture

The application follows a simple browser–server–database architecture:

1. **Presentation layer:** HTML pages, CSS, and JavaScript.
2. **Application layer:** PHP page handlers, authentication helpers, validation, CRUD operations, and JSON API endpoints.
3. **Data layer:** MySQL tables accessed through PDO.
4. **Asynchronous communication:** JavaScript Fetch requests send/receive JSON without a full page reload.

```text
Browser (HTML/CSS/JavaScript)
       |
       | normal page request / Fetch JSON request
       v
Apache + PHP
  |       |
  |       +-- Authentication and role checks
  |       +-- Form validation and CRUD handlers
  |       +-- JSON API endpoints
  |
  v
PDO database connection
       |
       v
MySQL: students, events, registrations, remember-me tokens*
```

`remember_me_tokens` is present only if the relevant authentication migration has been applied successfully.

## 6. Database design

### students
Stores account/student details. Main key: `student_id`. Email is unique. Passwords should be stored as hashes, not plaintext.

### events
Stores event title, description, date/time, venue, capacity, and creation time. Main key: `event_id`.

### registrations
Associates students with events. It has foreign keys to `students.student_id` and `events.event_id`, a registration status, and a unique constraint for each student/event pair.

### remember_me_tokens
If enabled by the authentication migration, stores a selector, validator hash, expiry time, and a foreign key to the student account. Raw validator values should not be stored in the database.

### ER relationship summary

```text
STUDENTS 1 --------< REGISTRATIONS >-------- 1 EVENTS
          one student can have many          one event can have many
          registration rows                  registration rows

STUDENTS 1 --------< REMEMBER_ME_TOKENS
          one student can have multiple token records
          (if remember-me migration is installed)
```

`registrations` resolves the many-to-many relationship between students and events.

## 7. Module summary

| Module | Description |
|---|---|
| Public website | Home, about, events, FAQ and related pages |
| Frontend interactivity | DOM manipulation, theme/UI behavior, and form validation |
| PHP form processing | Contact/feedback form handlers and CSV storage |
| Database connection | PDO configuration and schema |
| Registration | Input checks, duplicate-email check, password hashing |
| Authentication | Login/logout, sessions, role checks, timeout and remember-me support |
| Student CRUD | Create, list/search, update, and delete student records |
| Event CRUD | Create, list/search, update, and delete event records |
| JSON API | GET/POST/PUT/DELETE patterns for student and event data |
| Dynamic dashboard | Menu/dashboard differences by role |
| Final integration | Central route list, documentation, testing and submission preparation |

## 8. Security practices demonstrated

- Password hashing and password verification.
- Prepared SQL statements through PDO.
- Server-side validation.
- Role checks on protected routes.
- Escaping dynamic output.
- Session timeout and cookie flags in authentication helpers.
- Remember-me selector/validator design where migration is enabled.
- JSON API method checks and CSRF-token handling in the API demo.

Security still requires review in the running environment. These features should not be described as a formal security audit.

## 9. Testing approach

Testing should include functional tests, invalid input tests, role/permission tests, database persistence checks, responsive layout checks, and keyboard/accessibility checks. Record actual results in `TEST-REPORT.md`.

## 10. Conclusion

StudentHub combines the main concepts covered by the practical series into a single code folder. It provides a basis for demonstrating full-stack web development, database operations, authentication, CRUD, and asynchronous API usage. The final submission should include source code, reviewed SQL export, screenshots, a completed test report, and a GitHub repository.

## 11. Future enhancements

- Student event registration/cancellation UI.
- Email verification and password reset.
- Audit logs and pagination for large tables.
- More complete automated tests.
- Centralized CSRF protection for every state-changing form.
- Improved accessibility and responsive testing.
- Deployment to a properly configured PHP/MySQL hosting environment.
