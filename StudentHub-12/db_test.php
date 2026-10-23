<?php
require_once __DIR__ . '/config/db.php';

$studentCountStatement = $pdo->prepare(
    'SELECT COUNT(*) FROM students WHERE course = :course'
);
$studentCountStatement->execute([
    'course' => 'B.Tech Computer Science'
]);
$studentCount = (int) $studentCountStatement->fetchColumn();

$studentsStatement = $pdo->prepare(
    'SELECT student_id, full_name, email, course, study_year
     FROM students
     ORDER BY student_id ASC'
);
$studentsStatement->execute();
$students = $studentsStatement->fetchAll();

$eventsStatement = $pdo->prepare(
    'SELECT e.event_id, e.title, e.event_date, e.venue,
            COUNT(r.registration_id) AS registration_count
     FROM events AS e
     LEFT JOIN registrations AS r ON r.event_id = e.event_id
     GROUP BY e.event_id, e.title, e.event_date, e.venue
     ORDER BY e.event_date ASC'
);
$eventsStatement->execute();
$events = $eventsStatement->fetchAll();

function escapeHtml($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Database Test</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container page-section">
    <h1>StudentHub Database Test</h1>
    <p><strong>Connection status:</strong> Connected to MySQL successfully.</p>
    <p><strong>Students enrolled in B.Tech Computer Science:</strong>
        <?= escapeHtml($studentCount) ?></p>

    <h2>Students</h2>
    <?php if ($students): ?>
        <div class="table-responsive">
            <table>
                <thead><tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th>
                </tr></thead>
                <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= escapeHtml($student['student_id']) ?></td>
                        <td><?= escapeHtml($student['full_name']) ?></td>
                        <td><?= escapeHtml($student['email']) ?></td>
                        <td><?= escapeHtml($student['course'] ?? '') ?></td>
                        <td><?= escapeHtml($student['study_year'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No student records found. Import database/studenthub.sql first.</p>
    <?php endif; ?>

    <h2>Events and Registrations</h2>
    <?php if ($events): ?>
        <div class="table-responsive">
            <table>
                <thead><tr>
                    <th>Event ID</th><th>Event</th><th>Date and Time</th><th>Venue</th><th>Registrations</th>
                </tr></thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= escapeHtml($event['event_id']) ?></td>
                        <td><?= escapeHtml($event['title']) ?></td>
                        <td><?= escapeHtml($event['event_date']) ?></td>
                        <td><?= escapeHtml($event['venue']) ?></td>
                        <td><?= escapeHtml($event['registration_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No event records found. Import database/studenthub.sql first.</p>
    <?php endif; ?>

    <p><a href="index.html">Return to StudentHub</a></p>
</main>
</body>
</html>
