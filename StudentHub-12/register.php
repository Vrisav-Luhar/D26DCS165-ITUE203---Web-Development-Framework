<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config/db.php';

if (empty($_SESSION['registration_csrf'])) {
    $_SESSION['registration_csrf'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = '';
$fullName = '';
$email = '';
$mobile = '';
$course = '';
$year = '';

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string)($_POST['fullName'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $mobile = trim((string)($_POST['mobile'] ?? ''));
    $course = trim((string)($_POST['course'] ?? ''));
    $yearInput = trim((string)($_POST['year'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirmPassword'] ?? '');
    $csrf = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['registration_csrf'], $csrf)) {
        $errors[] = 'Your form session expired. Refresh the page and try again.';
    }

    if ($fullName === '' || mb_strlen($fullName) < 3 || mb_strlen($fullName) > 100) {
        $errors[] = 'Full name must be between 3 and 100 characters.';
    } elseif (!preg_match("/^[\p{L}][\p{L}\s.'-]{2,99}$/u", $fullName)) {
        $errors[] = 'Enter a valid name using letters, spaces, apostrophes, dots or hyphens.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $errors[] = 'Enter a valid email address (maximum 150 characters).';
    }

    if ($mobile !== '' && !preg_match('/^[0-9]{10,15}$/', $mobile)) {
        $errors[] = 'Mobile number must contain 10 to 15 digits.';
    }

    if ($course === '' || mb_strlen($course) > 100) {
        $errors[] = 'Please select a course.';
    }

    $year = filter_var($yearInput, FILTER_VALIDATE_INT);
    if ($year === false || $year < 1 || $year > 6) {
        $errors[] = 'Select a valid study year.';
        $year = '';
    }

    if (strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Password must be at least 8 characters and include uppercase, lowercase, a number and a special character.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password and confirm password do not match.';
    }

    if (!$errors) {
        try {
            $check = $pdo->prepare('SELECT student_id FROM students WHERE email = :email LIMIT 1');
            $check->execute(['email' => $email]);

            if ($check->fetch()) {
                $errors[] = 'An account with this email already exists. Please use another email.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare(
                    'INSERT INTO students (full_name, email, mobile, course, study_year, password_hash)
                     VALUES (:full_name, :email, :mobile, :course, :study_year, :password_hash)'
                );
                $insert->execute([
                    'full_name' => $fullName,
                    'email' => $email,
                    'mobile' => $mobile !== '' ? $mobile : null,
                    'course' => $course,
                    'study_year' => (int)$year,
                    'password_hash' => $passwordHash,
                ]);

                $success = 'Registration successful! Your StudentHub account has been created.';
                $fullName = $email = $mobile = $course = $year = '';
                $_SESSION['registration_csrf'] = bin2hex(random_bytes(32));
            }
        } catch (PDOException $exception) {
            // A unique database constraint is also kept as a final duplicate-email safeguard.
            error_log('StudentHub registration error: ' . $exception->getMessage());
            if ($exception->getCode() === '23000') {
                $errors[] = 'An account with this email already exists.';
            } else {
                $errors[] = 'Registration could not be completed right now. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure StudentHub account registration">
    <title>Secure Registration - StudentHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-container">
        <a href="index.html" class="logo">Student<span>Hub</span></a>
        <button class="hamburger" id="hamburger" aria-label="Open navigation menu" aria-expanded="false">☰</button>
        <nav class="main-nav" id="mainNav">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="events.html">Events</a>
            <a href="faq.html">FAQ</a>
            <a href="contact.php">Contact</a>
            <a href="login.html">Login</a>
            <button id="themeToggle" class="theme-toggle" type="button" aria-label="Toggle dark mode">🌙</button>
        </nav>
    </div>
</header>

<main>
    <section class="page-header">
        <div class="container">
            <h1>Secure Student Registration</h1>
            <p>Create an account. Your password is hashed before it is stored.</p>
        </div>
    </section>

    <section class="registration-section">
        <div class="container">
            <div class="form-card">
                <div class="form-heading">
                    <h2>Create Your Account</h2>
                    <p>Fields marked <span class="required">*</span> are required.</p>
                </div>

                <?php if ($success !== ''): ?>
                    <div class="success-message" role="status" aria-live="polite"><?= e($success) ?></div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="error-summary" role="alert" aria-live="assertive">
                        <strong>Please correct the following:</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="secureRegistrationForm" method="post" action="register.php">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['registration_csrf']) ?>">

                    <div class="form-group">
                        <label for="fullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="fullName" name="fullName" value="<?= e($fullName) ?>"
                               autocomplete="name" minlength="3" maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?= e($email) ?>"
                               autocomplete="email" maxlength="150" required>
                    </div>

                    <div class="form-group">
                        <label for="mobile">Mobile Number</label>
                        <input type="tel" id="mobile" name="mobile" value="<?= e($mobile) ?>"
                               autocomplete="tel" inputmode="numeric" pattern="[0-9]{10,15}"
                               title="Enter 10 to 15 digits">
                    </div>

                    <div class="form-group">
                        <label for="course">Course <span class="required">*</span></label>
                        <select id="course" name="course" required>
                            <option value="">Select your course</option>
                            <?php
                            $courses = ['B.Tech Computer Science', 'B.Tech Information Technology',
                                        'B.Tech AI & Machine Learning', 'B.Tech Computer Engineering',
                                        'Diploma Information Technology', 'Other'];
                            foreach ($courses as $option):
                            ?>
                                <option value="<?= e($option) ?>" <?= $course === $option ? 'selected' : '' ?>>
                                    <?= e($option) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="year">Study Year <span class="required">*</span></label>
                        <select id="year" name="year" required>
                            <option value="">Select study year</option>
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>" <?= (string)$year === (string)$i ? 'selected' : '' ?>>
                                    Year <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                               minlength="8" required aria-describedby="passwordHelp">
                        <small id="passwordHelp">At least 8 characters, including uppercase, lowercase, number and special character.</small>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                        <input type="password" id="confirmPassword" name="confirmPassword"
                               autocomplete="new-password" minlength="8" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Account</button>
                </form>

                <p style="margin-top:1rem">Already registered? <a href="login.html">Go to login</a>.</p>
                <p><a href="register.html">Open the original Practical 5 frontend-only form</a></p>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="container"><p>&copy; <?= date('Y') ?> StudentHub. Practical 9 secure registration demo.</p></div>
</footer>
<script src="js/script.js"></script>
</body>
</html>
