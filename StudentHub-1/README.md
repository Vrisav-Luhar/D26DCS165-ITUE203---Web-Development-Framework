# StudentHub — Practical 1: Project Planning

**Course:** ITUE203 — Web Development Fundamentals  
**Project:** StudentHub — College Student Portal

## Aim
Define the purpose, users, scope, and initial structure of the StudentHub website before implementation.

## Project overview
StudentHub is planned as a centralized college portal for students, faculty, and administrators. The portal is intended to provide access to academic information and common college services through role-appropriate pages.

## Objectives
- Plan a clear website structure and navigation.
- Identify the main user groups and their needs.
- List core portal features.
- Establish a foundation for the HTML, CSS, JavaScript, PHP, and MySQL practicals that follow.

## Intended users
- **Students:** access their own profile and permitted student services.
- **Faculty:** access authorized academic information and tasks.
- **Administrators:** manage student and event records and administer portal data.

## Scope
The planned portal includes public information pages, account registration/login, dashboards, student management, event management, and supporting forms. Some functionality is introduced in later practicals; this planning document does not claim that every feature is implemented in Practical 1.

## Sitemap
```text
StudentHub
├── Home (index.html)
├── About (about.html)
├── Events (events.html)
├── FAQ (faq.html)
├── Contact (contact.html)
├── Feedback (feedback.html)
├── Login (login.html)
├── Register (register.html)
├── Student Dashboard (student-dashboard.html)
├── Faculty Dashboard (faculty-dashboard.html)
├── Admin Dashboard (admin-dashboard.html)
└── Profile (profile.html)
```

## Initial requirements
### Functional
1. Visitors can navigate between public pages.
2. Visitors can view portal information and events.
3. Users have login and registration entry points.
4. The design anticipates different student, faculty, and administrator workflows.
5. Forms and data-management features can be added incrementally in later practicals.

### Non-functional
- Consistent visual design and navigation.
- Responsive layout for desktop and mobile.
- Clear labels and readable content.
- Maintainable folder structure.
- Server-side validation and access control when backend functionality is introduced.

## Proposed technology stack
- HTML5 for page structure
- CSS3 for presentation
- JavaScript for client-side interaction
- PHP for server-side processing
- MySQL for persistent data
- XAMPP for local development in later practicals

## Project folder plan
```text
StudentHub/
├── index.html
├── about.html
├── events.html
├── faq.html
├── contact.html
├── feedback.html
├── login.html
├── register.html
├── student-dashboard.html
├── faculty-dashboard.html
├── admin-dashboard.html
├── profile.html
├── css/
└── js/
```

## Expected outcome
A documented project plan, basic requirements, user groups, and sitemap that guide implementation in the next practicals.

## Later development
The website's actual page markup and styling are developed in Practical 2 and subsequent practicals. Backend functionality, database integration, CRUD, authentication, and APIs are covered in later practicals.
