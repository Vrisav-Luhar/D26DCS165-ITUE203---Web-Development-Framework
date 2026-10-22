# StudentHub Viva Questions and Short Answers

## Project and architecture

**1. What is StudentHub?**  
A full-stack student portal prototype combining public pages, authentication, role-aware dashboards, student/event CRUD, and a Fetch API demo.

**2. Which technologies are used?**  
HTML5, CSS3, JavaScript, PHP, MySQL, PDO, Fetch API, and XAMPP.

**3. Explain the three-layer architecture.**  
The browser presents HTML/CSS/JavaScript; PHP processes requests and business logic; MySQL stores persistent data.

## PHP and database

**4. What is PDO?**  
PHP Data Objects is a consistent interface for connecting to databases and running queries.

**5. Why use prepared statements?**  
They separate SQL structure from user-supplied values and help prevent SQL injection when used correctly.

**6. What is CRUD?**  
Create, Read, Update, Delete—the four basic data operations.

**7. Why is email unique?**  
It prevents multiple accounts from registering with the same email address at the database level.

**8. Why should passwords be hashed?**  
A password hash avoids storing the original password. Passwords should be verified using `password_verify()`.

## Authentication and security

**9. What is a session?**  
Server-side state associated with a session identifier, commonly used to remember the logged-in user between requests.

**10. What is role-based access control?**  
Access is granted according to a user's role, such as Admin or Student. The server must enforce it on protected operations.

**11. Why is hiding a menu link not enough?**  
A user can type a protected URL directly. The PHP page/API must check the user's role on every protected request.

**12. What is session timeout?**  
An inactivity limit after which the application ends the session and requires the user to authenticate again.

**13. What is CSRF?**  
Cross-Site Request Forgery attempts to cause an authenticated browser to perform an unwanted action. CSRF tokens help verify that state-changing requests originate from the intended application flow.

**14. Why escape output?**  
Escaping makes dynamic text safe for its output context and helps prevent HTML/script injection.

## JavaScript and API

**15. What is Fetch API?**  
A browser API for making asynchronous network requests and handling responses through promises.

**16. What is JSON?**  
JavaScript Object Notation, a text format for exchanging structured data.

**17. What is AJAX?**  
A general technique for communicating with the server asynchronously without reloading the whole page.

**18. What HTTP methods does the demo use?**  
GET to read, POST to create, PUT to update, and DELETE to remove records.

**19. Why check `response.ok`?**  
Fetch does not automatically reject its promise for HTTP error statuses. Code should check the response status before treating the result as successful.

## Testing and deployment

**20. What is integration testing?**  
Testing whether modules work together, such as login, role checks, database CRUD, and Fetch API operations.

**21. What should be in a test report?**  
Test case, expected result, actual result, pass/fail status, evidence, and notes.

**22. What is GitHub used for?**  
Hosting a Git repository for version control, collaboration, and submission.

**23. Why not commit a live database dump?**  
It may expose personal data, credentials, or sensitive test records.

**24. Is XAMPP public production hosting?**  
No. It is primarily a local development stack. Public deployment requires production configuration, HTTPS, secure credentials, backups, and additional testing.
