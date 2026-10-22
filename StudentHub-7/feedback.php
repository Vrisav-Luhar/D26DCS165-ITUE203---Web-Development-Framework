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
<title>Feedback | StudentHub</title>
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
<li><a href="contact.php">Contact</a></li>
<li><a href="feedback.php" aria-current="page">Feedback</a></li>
<li><a href="login.html">Login</a></li>
<li><a href="register.html">Register</a></li>
</ul>
</nav>
</div>
</header>

<main id="main-content">
<section class="page-section">
<div class="container">
<h1>StudentHub Feedback</h1>
<p>Help us improve StudentHub by sharing your feedback.</p>

<?php if ($status === 'success'): ?>
<div class="form-card" role="alert">
<strong>Feedback Submitted Successfully!</strong>
<p>Thank you for sharing your feedback.</p>
</div>
<?php elseif ($status === 'error'): ?>
<div class="form-card" role="alert">
<strong>Feedback Could Not Be Submitted</strong>
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

<section class="form-card">
<form id="feedbackForm" action="process/feedback_process.php" method="post">
<div class="form-group">
<label for="feedbackName">Full Name <span class="required">*</span></label>
<input id="feedbackName" name="name" type="text" minlength="3" maxlength="50" autocomplete="name" required>
<small id="feedbackNameError" class="form-error"></small>
</div>

<div class="form-group">
<label for="feedbackEmail">Email Address <span class="required">*</span></label>
<input id="feedbackEmail" name="email" type="email" autocomplete="email" required>
<small id="feedbackEmailError" class="form-error"></small>
</div>

<fieldset class="form-group">
<legend>Overall Rating <span class="required">*</span></legend>
<div class="radio-group">
<label><input type="radio" name="rating" value="1"> 1 - Poor</label>
<label><input type="radio" name="rating" value="2"> 2 - Fair</label>
<label><input type="radio" name="rating" value="3"> 3 - Good</label>
<label><input type="radio" name="rating" value="4"> 4 - Very Good</label>
<label><input type="radio" name="rating" value="5"> 5 - Excellent</label>
</div>
<small id="ratingError" class="form-error"></small>
</fieldset>

<div class="form-group">
<label for="category">Feedback Category <span class="required">*</span></label>
<select id="category" name="category" required>
<option value="">Select a category</option>
<option value="Website">Website</option>
<option value="Events">Events</option>
<option value="Student Services">Student Services</option>
<option value="User Experience">User Experience</option>
<option value="Other">Other</option>
</select>
<small id="categoryError" class="form-error"></small>
</div>

<div class="form-group">
<label for="feedbackMessage">Your Feedback <span class="required">*</span></label>
<textarea id="feedbackMessage" name="feedback" rows="6" minlength="10" maxlength="1000" required></textarea>
<small id="feedbackMessageError" class="form-error"></small>
</div>

<button class="button" type="submit">Submit Feedback</button>
</form>
</section>
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
    const form = document.getElementById('feedbackForm');
    if (!form) return;

    const name = document.getElementById('feedbackName');
    const email = document.getElementById('feedbackEmail');
    const category = document.getElementById('category');
    const feedback = document.getElementById('feedbackMessage');

    const setError = (input, errorId, text) => {
        const error = document.getElementById(errorId);
        error.textContent = text;
        if (input) input.classList.toggle('input-error', !!text);
    };

    const validateName = () => {
        const valid = /^[A-Za-z][A-Za-z\s.'-]{2,49}$/.test(name.value.trim());
        setError(name, 'feedbackNameError', valid ? '' : 'Please enter a valid name.');
        return valid;
    };

    const validateEmail = () => {
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value.trim());
        setError(email, 'feedbackEmailError', valid ? '' : 'Please enter a valid email address.');
        return valid;
    };

    const validateRating = () => {
        const selected = document.querySelector('input[name="rating"]:checked');
        document.getElementById('ratingError').textContent = selected ? '' : 'Please select a rating.';
        return !!selected;
    };

    const validateCategory = () => {
        const valid = category.value !== '';
        setError(category, 'categoryError', valid ? '' : 'Please select a feedback category.');
        return valid;
    };

    const validateFeedback = () => {
        const valid = feedback.value.trim().length >= 10;
        setError(feedback, 'feedbackMessageError', valid ? '' : 'Feedback must contain at least 10 characters.');
        return valid;
    };

    name.addEventListener('input', validateName);
    email.addEventListener('input', validateEmail);
    category.addEventListener('change', validateCategory);
    feedback.addEventListener('input', validateFeedback);
    document.querySelectorAll('input[name="rating"]').forEach(function (radio) {
        radio.addEventListener('change', validateRating);
    });

    form.addEventListener('submit', function (event) {
        const valid = validateName() && validateEmail() && validateRating() && validateCategory() && validateFeedback();
        if (!valid) event.preventDefault();
    });
});
</script>
</body>
</html>
