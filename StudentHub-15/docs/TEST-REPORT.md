# StudentHub Final Test Report — Practical 15

**Project:** StudentHub  
**Tester:** __________________________  
**Date tested:** _____________________  
**Environment:** Windows / XAMPP  
**Browser:** _________________________  
**PHP version:** _____________________  
**MySQL version:** ___________________

The user reported that all pages are working. Record the specific checks and evidence below before final submission. Do not mark a test as passed unless it was actually checked.

| No. | Test case | Expected result | Result | Evidence / notes |
|---:|---|---|---|---|
| 1 | Home page opens | Page renders without errors | User-reported working; verify screenshot | |
| 2 | Public navigation | Links open intended pages | User-reported working; verify links | |
| 3 | Registration with valid data | Account is created | User-reported working; verify database | |
| 4 | Duplicate email registration | Duplicate is rejected | Not separately confirmed | |
| 5 | Password stored as hash | Database does not contain plaintext password | Not separately confirmed | |
| 6 | Valid login | User session begins | User-reported working; verify role | |
| 7 | Invalid login | Login is rejected safely | Not separately confirmed | |
| 8 | Logout | Session ends | User-reported working; verify protected page | |
| 9 | Session timeout | Inactive session expires as configured | Not separately confirmed | |
| 10 | Remember-me behavior | Valid token restores session; invalid/expired token does not | Not separately confirmed | |
| 11 | Admin dashboard | Admin-specific dashboard loads | User-reported working | |
| 12 | Student dashboard | Student-specific dashboard loads | User-reported working | |
| 13 | Student CRUD | Create/read/update/delete works | User-reported working; verify each operation | |
| 14 | Student search | Matching records are returned | Not separately confirmed | |
| 15 | Event CRUD | Create/read/update/delete works | User-reported working; verify each operation | |
| 16 | Event with registrations | Protected deletion is handled safely | Not separately confirmed | |
| 17 | Fetch API list | Student/event data appears asynchronously | User-reported working | |
| 18 | Fetch API create | New record persists and list refreshes | User-reported working | |
| 19 | Fetch API update/delete | Changes persist and errors are handled | Not separately confirmed | |
| 20 | Student attempts admin route | Access is denied server-side | Not separately confirmed | |
| 21 | Invalid API input | Validation returns a safe error | Not separately confirmed | |
| 22 | Database persistence | Record remains after page refresh | Not separately confirmed | |
| 23 | Responsive layout | UI usable at narrow widths | Not separately confirmed | |
| 24 | Keyboard/accessibility | Focus, labels and errors are usable | Not separately confirmed | |
| 25 | SQL backup | Reviewed SQL export is stored safely | Not yet confirmed | |
| 26 | GitHub submission | Repository pushed and README opens | Not yet confirmed | |

## Summary
- Tests passed: ______
- Tests failed: ______
- Tests not separately confirmed: ______
- Known issues: ______________________________________
- Final decision: Ready for submission / Needs fixes

## Evidence checklist
- [ ] Home page screenshot
- [ ] Registration/login screenshot (do not include passwords)
- [ ] Admin dashboard screenshot
- [ ] Student dashboard screenshot
- [ ] Student CRUD screenshot
- [ ] Event CRUD screenshot
- [ ] Fetch API screenshot showing successful operation
- [ ] Database tables or query result screenshot with sensitive data obscured
- [ ] GitHub repository screenshot/link
