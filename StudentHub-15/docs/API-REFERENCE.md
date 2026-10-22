# StudentHub JSON API Reference

This describes the intended API usage in the Practical 13 demo. Verify each response against the final local code before relying on it for external clients.

## Base paths

- `api/students.php`
- `api/events.php`

The API is intended for authenticated Admin use. Successful and error responses are JSON. Write operations from `api-demo.php` include a CSRF header.

## Student endpoint

| Method | Purpose | Request |
|---|---|---|
| GET | List students | Optional `?q=search-term` |
| POST | Create student | JSON body with required student fields |
| PUT | Update student | JSON body including `student_id` and fields to update |
| DELETE | Delete student | JSON body containing `student_id` |

The student fields used by the demo include `full_name`, `email`, `mobile`, `course`, `study_year`, and `password`. Password input should be hashed by the server before storage. The API should reject duplicate emails and invalid values.

Example GET:

```http
GET /StudentHub-15/api/students.php?q=computer
Accept: application/json
```

Example POST body:

```json
{
  "full_name": "Sample Student",
  "email": "sample.student@example.com",
  "mobile": "9876543210",
  "course": "B.Tech Computer Science",
  "study_year": 2,
  "password": "Replace-With-A-Strong-Test-Password!"
}
```

## Event endpoint

| Method | Purpose | Request |
|---|---|---|
| GET | List events | Optional `?q=search-term` |
| POST | Create event | JSON body with title, description, event date, venue, capacity |
| PUT | Update event | JSON body including `event_id` and fields to update |
| DELETE | Delete event | JSON body containing `event_id` |

Example event body:

```json
{
  "title": "Sample Web Workshop",
  "description": "A demonstration event for testing.",
  "event_date": "2027-02-10T10:30",
  "venue": "Computer Lab",
  "capacity": 40
}
```

## Response pattern

Success responses use a `success` flag and may include `message` and/or `data`. Errors should include `success: false` and a safe explanatory message. Common HTTP statuses include 200, 201, 400, 403, 404, 405, 409, 422, and 500 depending on the endpoint and error.

## CSRF and authentication

The demo obtains a CSRF token from the authenticated PHP session and sends it in `X-CSRF-Token` for write operations. Requests must include the same-origin session cookies. Do not expose tokens in public logs or documentation.

## Example Fetch pattern

```javascript
const response = await fetch('api/events.php', {
  headers: { 'Accept': 'application/json' },
  credentials: 'same-origin'
});
const result = await response.json();

if (!response.ok || !result.success) {
  throw new Error(result.message || 'Request failed');
}
console.log(result.data);
```

This example is illustrative; the actual page includes shared request handling and CSRF logic.
