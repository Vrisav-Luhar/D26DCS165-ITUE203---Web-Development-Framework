<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../feedback.php');
    exit;
}

function cleanText($value) {
    return trim(strip_tags((string)$value));
}

$name = cleanText($_POST['name'] ?? '');
$email = cleanText($_POST['email'] ?? '');
$rating = cleanText($_POST['rating'] ?? '');
$category = cleanText($_POST['category'] ?? '');
$feedback = cleanText($_POST['feedback'] ?? '');

$errors = [];

if ($name === '') {
    $errors[] = 'Name is required.';
} elseif (!preg_match("/^[A-Za-z][A-Za-z\s.'-]{2,49}$/", $name)) {
    $errors[] = 'Please enter a valid name.';
}

if ($email === '') {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (!in_array($rating, ['1', '2', '3', '4', '5'], true)) {
    $errors[] = 'Please select a valid rating.';
}

$allowedCategories = ['Website', 'Events', 'Student Services', 'User Experience', 'Other'];
if (!in_array($category, $allowedCategories, true)) {
    $errors[] = 'Please select a valid feedback category.';
}

if ($feedback === '') {
    $errors[] = 'Feedback is required.';
} elseif (strlen($feedback) < 10 || strlen($feedback) > 1000) {
    $errors[] = 'Feedback must contain 10 to 1000 characters.';
}

if ($errors) {
    header('Location: ../feedback.php?status=error&errors=' . urlencode(implode('|', $errors)));
    exit;
}

$dataDirectory = dirname(__DIR__) . '/data';
$dataFile = $dataDirectory . '/feedback.csv';

if (!is_dir($dataDirectory) && !mkdir($dataDirectory, 0755, true)) {
    header('Location: ../feedback.php?status=error&message=' . urlencode('Unable to create data directory.'));
    exit;
}

$file = fopen($dataFile, 'a');
if ($file === false) {
    header('Location: ../feedback.php?status=error&message=' . urlencode('Unable to open feedback storage file.'));
    exit;
}

if (!flock($file, LOCK_EX)) {
    fclose($file);
    header('Location: ../feedback.php?status=error&message=' . urlencode('Unable to lock feedback storage file.'));
    exit;
}

if (filesize($dataFile) === 0) {
    fputcsv($file, ['Name', 'Email', 'Rating', 'Category', 'Feedback', 'Submitted At']);
}

$success = fputcsv($file, [$name, $email, $rating, $category, $feedback, date('Y-m-d H:i:s')]);
flock($file, LOCK_UN);
fclose($file);

if ($success === false) {
    header('Location: ../feedback.php?status=error&message=' . urlencode('Feedback could not be saved.'));
    exit;
}

header('Location: ../feedback.php?status=success');
exit;
?>
