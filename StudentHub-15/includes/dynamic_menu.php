<?php
declare(strict_types=1);

/**
 * StudentHub Practical 14: dynamic navigation menu.
 * Call only after config/auth.php has started the session and loaded $pdo.
 */
function studenthub_render_menu(string $active = ''): void
{
    $role = (string)($_SESSION['role'] ?? 'student');
    $isAdmin = ($role === 'admin');

    $items = [
        ['label' => 'Dashboard', 'href' => $isAdmin ? 'admin-dashboard.php' : 'student-dashboard.php', 'key' => 'dashboard'],
        ['label' => 'Events', 'href' => 'admin/events.php', 'key' => 'events'],
        ['label' => 'Profile', 'href' => 'profile.php', 'key' => 'profile'],
    ];

    if ($isAdmin) {
        $items[] = ['label' => 'Manage Students', 'href' => 'admin/students.php', 'key' => 'students'];
        $items[] = ['label' => 'Manage Events', 'href' => 'admin/events.php', 'key' => 'manage-events'];
        $items[] = ['label' => 'Fetch API Demo', 'href' => 'api-demo.php', 'key' => 'api'];
        $items[] = ['label' => 'Database Check', 'href' => 'db-check.php', 'key' => 'database'];
    }

    echo '<nav class="main-nav" aria-label="Main navigation">';
    foreach ($items as $item) {
        // Student role should not see admin-only links.
        if (!$isAdmin && in_array($item['key'], ['students', 'manage-events', 'api', 'database'], true)) {
            continue;
        }
        $href = htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
        $current = $active === $item['key'] ? ' aria-current="page"' : '';
        echo '<a href="' . $href . '"' . $current . '>' . $label . '</a>';
    }
    echo '<a href="logout.php">Logout</a>';
    echo '</nav>';
}
