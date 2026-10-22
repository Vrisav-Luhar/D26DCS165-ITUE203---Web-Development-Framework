<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../contact.php');
    exit;
}

function cleanText($value) {
    return trim(strip_tags((string)$value));
}

$name = cleanText($_POST['name'] ?? '');
$email = cleanText($_POST['email'] ?? '');
$subject = cleanText($_POST['subject'] ?? '');
$message = cleanText($_POST['message'] ?? '');

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

if ($subject === '') {
    $errors[] = 'Subject is required.';
} elseif (strlen($subject) < 3 || strlen($subject) > 100) {
    $errors[] = 'Subject must contain 3 to 100 characters.';
}

if ($message === '') {
    $errors[] = 'Message is required.';
} elseif (strlen($message) < 10 || strlen($message) > 1000) {
    $errors[] = 'Message must contain 10 to 1000 characters.';
}

if ($errors) {
    header('Location: ../contact.php?status=error&errors=' . urlencode(implode('|', $errors)));
    exit;
}

$dataDirectory = dirname(__DIR__) . '/data';
$dataFile = $dataDirectory . '/contacts.csv';

if (!is_dir($dataDirectory) && !mkdir($dataDirectory, 0755, true)) {
    header('Location: ../contact.php?status=error&message=' . urlencode('Unable to create data directory.'));
    exit;
}

$file = fopen($dataFile, 'a');
if ($file === false) {
    header('Location: ../contact.php?status=error&message=' . urlencode('Unable to open contact storage file.'));
    exit;
}

if (!flock($file, LOCK_EX)) {
    fclose($file);
    header('Location: ../contact.php?status=error&message=' . urlencode('Unable to lock contact storage file.'));
    exit;
}

if (filesize($dataFile) === 0) {
    fputcsv($file, ['Name', 'Email', 'Subject', 'Message', 'Submitted At']);
}

$success = fputcsv($file, [$name, $email, $subject, $message, date('Y-m-d H:i:s')]);
flock($file, LOCK_UN);
fclose($file);

if ($success === false) {
    header('Location: ../contact.php?status=error&message=' . urlencode('Message could not be saved.'));
    exit;
}

header('Location: ../contact.php?status=success');
exit;
?>
