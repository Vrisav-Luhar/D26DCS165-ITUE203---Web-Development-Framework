<?php
declare(strict_types=1);

/** Validate and store a poster. Returns a relative path, null if no file was selected. */
function studenthub_store_event_poster(array $file, array &$errors): ?string {
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) return null;
    if ($error !== UPLOAD_ERR_OK) {
        $errors[] = 'Poster upload failed. Choose an image file and try again.';
        return null;
    }
    $maxBytes = 2 * 1024 * 1024;
    if ((int)($file['size'] ?? 0) < 1 || (int)$file['size'] > $maxBytes) {
        $errors[] = 'Poster must be a valid image no larger than 2 MB.';
        return null;
    }
    $tmp = (string)($file['tmp_name'] ?? '');
    if (!is_uploaded_file($tmp)) {
        $errors[] = 'The uploaded poster could not be verified.';
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime]) || @getimagesize($tmp) === false) {
        $errors[] = 'Invalid poster type. Upload JPG, PNG, or WEBP only.';
        return null;
    }
    $directory = dirname(__DIR__) . '/uploads/event-posters';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        $errors[] = 'Poster storage directory could not be created.';
        return null;
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($tmp, $directory . '/' . $filename)) {
        $errors[] = 'Poster could not be saved. Check folder permissions.';
        return null;
    }
    return 'uploads/event-posters/' . $filename;
}

function studenthub_delete_event_poster(?string $path): void {
    if (!$path || !preg_match('~^uploads/event-posters/[a-f0-9]{32}\.(?:jpg|png|webp)$~', $path)) return;
    $full = dirname(__DIR__) . '/' . $path;
    if (is_file($full)) @unlink($full);
}
