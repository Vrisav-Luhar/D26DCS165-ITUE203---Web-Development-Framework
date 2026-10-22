<?php
// ==========================================================
// 1. Allow POST requests only
// ==========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../contact.php");

    exit();

}


// ==========================================================
// 2. Sanitization function
// ==========================================================

function cleanText($value)
{
    $value = trim($value);

    $value = strip_tags($value);

    return $value;
}


// ==========================================================
// 3. Get submitted values
// ==========================================================

$name =
    cleanText($_POST["name"] ?? "");

$email =
    cleanText($_POST["email"] ?? "");

$subject =
    cleanText($_POST["subject"] ?? "");

$message =
    cleanText($_POST["message"] ?? "");


// ==========================================================
// 4. Error array
// ==========================================================

$errors = [];


// ==========================================================
// 5. Validate Name
// ==========================================================

$namePattern =
    "/^[A-Za-z][A-Za-z\s.'-]{2,49}$/";


if ($name === "") {

    $errors[] =
        "Name is required.";

}
elseif (!preg_match($namePattern, $name)) {

    $errors[] =
        "Please enter a valid name.";

}


// ==========================================================
// 6. Validate Email
// ==========================================================

if ($email === "") {

    $errors[] =
        "Email address is required.";

}
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errors[] =
        "Please enter a valid email address.";

}


// ==========================================================
// 7. Validate Subject
// ==========================================================

if ($subject === "") {

    $errors[] =
        "Subject is required.";

}
elseif (strlen($subject) < 3) {

    $errors[] =
        "Subject must contain at least 3 characters.";

}
elseif (strlen($subject) > 100) {

    $errors[] =
        "Subject cannot exceed 100 characters.";

}


// ==========================================================
// 8. Validate Message
// ==========================================================

if ($message === "") {

    $errors[] =
        "Message is required.";

}
elseif (strlen($message) < 10) {

    $errors[] =
        "Message must contain at least 10 characters.";

}
elseif (strlen($message) > 1000) {

    $errors[] =
        "Message cannot exceed 1000 characters.";

}


// ==========================================================
// 9. Return validation errors
// ==========================================================

if (!empty($errors)) {

    $errorString =
        implode("|", $errors);

    header(
        "Location: ../contact.php?status=error&errors="
        . urlencode($errorString)
    );

    exit();

}


// ==========================================================
// 10. Data directory
// ==========================================================

$dataDirectory =
    dirname(__DIR__) . "/data";


// ==========================================================
// 11. CSV file
// ==========================================================

$dataFile =
    $dataDirectory . "/contacts.csv";


// ==========================================================
// 12. Create data directory if required
// ==========================================================

if (!is_dir($dataDirectory)) {

    if (!mkdir($dataDirectory, 0755, true)) {

        header(
            "Location: ../contact.php?status=error&message="
            . urlencode(
                "Unable to create data directory."
            )
        );

        exit();

    }

}


// ==========================================================
// 13. Open CSV file
// ==========================================================

$file = fopen($dataFile, "a");


if ($file === false) {

    header(
        "Location: ../contact.php?status=error&message="
        . urlencode(
            "Unable to open contact storage file."
        )
    );

    exit();

}


// ==========================================================
// 14. Lock file
// ==========================================================

if (!flock($file, LOCK_EX)) {

    fclose($file);

    header(
        "Location: ../contact.php?status=error&message="
        . urlencode(
            "Unable to lock contact storage file."
        )
    );

    exit();

}


// ==========================================================
// 15. Add CSV header if file is empty
// ==========================================================

if (filesize($dataFile) === 0) {

    fputcsv(
        $file,
        [
            "Name",
            "Email",
            "Subject",
            "Message",
            "Submitted At"
        ]
    );

}


// ==========================================================
// 16. Get current date and time
// ==========================================================

$submittedAt =
    date("Y-m-d H:i:s");


// ==========================================================
// 17. Write record
// ==========================================================

$success =
    fputcsv(
        $file,
        [
            $name,
            $email,
            $subject,
            $message,
            $submittedAt
        ]
    );


// ==========================================================
// 18. Unlock and close
// ==========================================================

flock($file, LOCK_UN);

fclose($file);


// ==========================================================
// 19. Check writing result
// ==========================================================

if ($success === false) {

    header(
        "Location: ../contact.php?status=error&message="
        . urlencode(
            "Message could not be saved."
        )
    );

    exit();

}


// ==========================================================
// 20. Successful submission
// ==========================================================

header(
    "Location: ../contact.php?status=success"
);

exit();

?>