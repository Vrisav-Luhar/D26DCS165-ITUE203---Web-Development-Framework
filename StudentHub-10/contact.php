<?php
$status = $_GET['status'] ?? '';
$message = $_GET['message'] ?? '';
$errors = [];

if (isset($_GET['errors']) && $_GET['errors'] !== '') {
    $errors = explode('|', $_GET['errors']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact | StudentHub</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-header">
<div class="header-inner">
<div class="brand-row">
<div>
<a class="brand" href="index.html">StudentHub</a>
<p class="tagline">College Student Portal</p>
</div>
<div class="header-actions">
<button id="themeToggle" class="theme-toggle" type="button">🌙 Dark Mode</button>
<button id="menuToggle" class="menu-toggle" type="button" aria-controls="siteNav" aria-expanded="false">☰</button>
</div>
</div>
<nav id="siteNav" class="site-nav" aria-label="Main navigation">
<ul>
<li><a href="index.html">Home</a></li>
<li><a href="about.html">About</a></li>
<li><a href="events.html">Events</a></li>
<li><a href="faq.html">FAQ</a></li>
<li><a href="contact.php" aria-current="page">Contact</a></li>
<li><a href="feedback.php">Feedback</a></li>
<li><a href="login.html">Login</a></li>
<li><a href="register.html">Register</a></li>
</ul>
</nav>
</div>
</header>

<main id="main-content">
<section class="page-section">
<div class="container">
<h1>Contact StudentHub</h1>
<p>Have a question or need assistance? Send us a message.</p>

<?php if ($status === 'success'): ?>
<div class="form-card" role="alert">
<strong>Message Sent Successfully!</strong>
<p>Your message has been recorded successfully.</p>
</div>
<?php elseif ($status === 'error'): ?>
<div class="form-card" role="alert">
<strong>Message Could Not Be Sent</strong>
<?php if (!empty($errors)): ?>
<ul>
<?php foreach ($errors as $error): ?>
<li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>
<?php elseif ($message !== ''): ?>
<p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
</div>
<?php endif; ?>

<div class="grid">
<article class="info-card">
<h2>StudentHub Support Office</h2>
<p><strong>Location:</strong><br>College Campus<br>Gujarat, India</p>
<p><strong>Email:</strong><br><a href="mailto:support@studenthub.example">support@studenthub.example</a></p>
<p><strong>Phone:</strong><br><a href="tel:+910000000000">+91 00000 00000</a></p>
</article>

<section class="form-card">
<h2>Send a Message</h2>
<p>All fields marked with <span class="required">*</span> are required.</p>

<form id="contactForm" action="process/contact_process.php" method="post">
<div class="form-group">
<label for="contactName">Full Name <span class="required">*</span></label>
<input id="contactName" name="name" type="text" minlength="3" maxlength="50" autocomplete="name" required>
<small id="contactNameError" class="form-error"></small>
</div>

<div class="form-group">
<label for="contactEmail">Email Address <span class="required">*</span></label>
<input id="contactEmail" name="email" type="email" autocomplete="email" required>
<small id="contactEmailError" class="form-error"></small>
</div>

<div class="form-group">
<label for="contactSubject">Subject <span class="required">*</span></label>
<input id="contactSubject" name="subject" type="text" minlength="3" maxlength="100" required>
<small id="contactSubjectError" class="form-error"></small>
</div>

<div class="form-group">
<label for="contactMessage">Message <span class="required">*</span></label>
<textarea id="contactMessage" name="message" rows="6" minlength="10" maxlength="1000" required></textarea>
<small id="contactMessageError" class="form-error"></small>
</div>

<button class="button" type="submit">Send Message</button>
</form>
</section>
</div>
</div>
</section>
</main>

<footer class="site-footer">
<div class="footer-inner">
<p><strong>StudentHub</strong> — College Student Portal</p>
<p>&copy; 2026 StudentHub. All rights reserved.</p>
</div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    if (!form) return;

    const name = document.getElementById('contactName');
    const email = document.getElementById('contactEmail');
    const subject = document.getElementById('contactSubject');
    const message = document.getElementById('contactMessage');

    const setError = (input, errorId, text) => {
        const error = document.getElementById(errorId);
        error.textContent = text;
        input.classList.toggle('input-error', !!text);
    };

    const validateName = () => {
        const value = name.value.trim();
        if (!/^[A-Za-z][A-Za-z\s.'-]{2,49}$/.test(value)) {
            setError(name, 'contactNameError', 'Please enter a valid name.');
            return false;
        }
        setError(name, 'contactNameError', '');
        return true;
    };

    const validateEmail = () => {
        const value = email.value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
            setError(email, 'contactEmailError', 'Please enter a valid email address.');
            return false;
        }
        setError(email, 'contactEmailError', '');
        return true;
    };

    const validateSubject = () => {
        const value = subject.value.trim();
        if (value.length < 3) {
            setError(subject, 'contactSubjectError', 'Subject must contain at least 3 characters.');
            return false;
        }
        setError(subject, 'contactSubjectError', '');
        return true;
    };

    const validateMessage = () => {
        const value = message.value.trim();
        if (value.length < 10) {
            setError(message, 'contactMessageError', 'Message must contain at least 10 characters.');
            return false;
        }
        setError(message, 'contactMessageError', '');
        return true;
    };

    name.addEventListener('input', validateName);
    email.addEventListener('input', validateEmail);
    subject.addEventListener('input', validateSubject);
    message.addEventListener('input', validateMessage);

    form.addEventListener('submit', function (event) {
        const valid = validateName() && validateEmail() && validateSubject() && validateMessage();
        if (!valid) event.preventDefault();
    });
});
</script>
</body>
</html>
