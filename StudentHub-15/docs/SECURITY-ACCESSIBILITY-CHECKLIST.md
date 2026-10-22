# Security and Accessibility Checklist

Complete this checklist by inspecting and testing the running project.

## Security
- [ ] Passwords use `password_hash()` and `password_verify()`.
- [ ] Duplicate email addresses are rejected by application logic and database constraints.
- [ ] PDO prepared statements are used for queries that include user input.
- [ ] Dynamic output is escaped before it is inserted into HTML.
- [ ] Every protected page checks authentication on the server.
- [ ] Every admin-only operation checks the role on the server.
- [ ] Logout invalidates the session and clears applicable cookies.
- [ ] Session timeout is tested.
- [ ] Remember-me tokens are stored as hashes and expire/rotate as intended.
- [ ] State-changing endpoints verify CSRF tokens where required.
- [ ] API methods reject unsupported HTTP methods.
- [ ] Invalid input receives safe validation messages.
- [ ] PHP/SQL exception details are not displayed to ordinary users.
- [ ] Diagnostic pages are restricted or removed before public deployment.
- [ ] Uploads, if present, validate size, MIME/type, extension and generated filenames.
- [ ] SQL export contains no real passwords, secrets, or unnecessary personal data.
- [ ] Git repository contains no credentials, cookies, tokens, or environment secrets.
- [ ] Public deployment uses HTTPS and least-privilege database credentials.

## Accessibility and usability
- [ ] Form fields have visible labels.
- [ ] Keyboard focus is visible.
- [ ] Navigation is usable with a keyboard.
- [ ] Buttons and links have descriptive text.
- [ ] Validation errors are shown in text, not color alone.
- [ ] Tables have meaningful header cells.
- [ ] Heading levels are logical.
- [ ] Text contrast is readable.
- [ ] Layout works at desktop and mobile/narrow widths.
- [ ] Status messages can be noticed by assistive technology.

## Deployment
- [ ] Apache and MySQL start successfully.
- [ ] Database credentials match the target environment.
- [ ] SQL backup is created and stored securely.
- [ ] Setup instructions were tested from a clean copy.
- [ ] README is included in the repository.
- [ ] Test report records actual results and evidence.
- [ ] Diagnostic/debug files are not exposed on a public deployment.
