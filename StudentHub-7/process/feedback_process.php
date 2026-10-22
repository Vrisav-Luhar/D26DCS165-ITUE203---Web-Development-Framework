<?php
// ==========================================================
// 1. Allow POST requests only
// ==========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../feedback.php");

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

$rating =
    cleanText($_POST["rating"] ?? "");

$category =
    cleanText($_POST["category"] ?? "");

$feedback =
    cleanText($_POST["feedback"] ?? "");


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
// 7. Validate Rating
// ==========================================================

$allowedRatings = [
    "1",
    "2",
    "3",
    "4",
    "5"
];


if ($rating === "") {

    $errors[] =
        "Please select a rating.";

}
elseif (!in_array($rating, $allowedRatings, true)) {

    $errors[] =
        "Invalid rating selected.";

}


// ==========================================================
// 8. Validate Category
// ==========================================================

$allowedCategories = [
    "Website",
    "Events",
    "Student Services",
    "User Experience",
    "Other"
];


if ($category === "") {

    $errors[] =
        "Please select a feedback category.";

}
elseif (
    !in_array(
        $category,
        $allowedCategories,
        true
    )
) {

    $errors[] =
        "Invalid feedback category selected.";

}


// ==========================================================
// 9. Validate Feedback
// ==========================================================

if ($feedback === "") {

    $errors[] =
        "Feedback is required.";

}
elseif (strlen($feedback) < 10) {

    $errors[] =
        "Feedback must contain at least 10 characters.";

}
elseif (strlen($feedback) > 1000) {

    $errors[] =
        "Feedback cannot exceed 1000 characters.";

}


// ==========================================================
// 10. Return validation errors
// ==========================================================

if (!empty($errors)) {

    $errorString =
        implode("|", $errors);

    header(
        "Location: ../feedback.php?status=error&errors="
        . urlencode($errorString)
    );

    exit();

}


// ==========================================================
// 11. Data directory
// ==========================================================

$dataDirectory =
    dirname(__DIR__) . "/data";


// ==========================================================
// 12. CSV file
// ==========================================================

$dataFile =
    $dataDirectory . "/feedback.csv";


// ==========================================================
// 13. Create data directory if required
// ==========================================================

if (!is_dir($dataDirectory)) {

    if (!mkdir($dataDirectory, 0755, true)) {

        header(
            "Location: ../feedback.php?status=error&message="
            . urlencode(
                "Unable to create data directory."
            )
        );

        exit();

    }

}


// ==========================================================
// 14. Open CSV file
// ==========================================================

$file =
    fopen($dataFile, "a");


if ($file === false) {

    header(
        "Location: ../feedback.php?status=error&message="
        . urlencode(
            "Unable to open feedback storage file."
        )
    );

    exit();

}


// ==========================================================
// 15. Lock file
// ==========================================================

if (!flock($file, LOCK_EX)) {

    fclose($file);

    header(
        "Location: ../feedback.php?status=error&message="
        . urlencode(
            "Unable to lock feedback storage file."
        )
    );

    exit();

}


// ==========================================================
// 16. Add CSV header
// ==========================================================

if (filesize($dataFile) === 0) {

    fputcsv(
        $file,
        [
            "Name",
            "Email",
            "Rating",
            "Category",
            "Feedback",
            "Submitted At"
        ]
    );

}


// ==========================================================
// 17. Timestamp
// ==========================================================

$submittedAt =
    date("Y-m-d H:i:s");


// ==========================================================
// 18. Write feedback
// ==========================================================

$success =
    fputcsv(
        $file,
        [
            $name,
            $email,
            $rating,
            $category,
            $feedback,
            $submittedAt
        ]
    );


// ==========================================================
// 19. Unlock and close
// ==========================================================

flock($file, LOCK_UN);

fclose($file);


// ==========================================================
// 20. Check writing result
// ==========================================================

if ($success === false) {

    header(
        "Location: ../feedback.php?status=error&message="
        . urlencode(
            "Feedback could not be saved."
        )
    );

    exit();

}


// ==========================================================
// 21. Successful submission
// ==========================================================

header(
    "Location: ../feedback.php?status=success"
);

exit();

?>