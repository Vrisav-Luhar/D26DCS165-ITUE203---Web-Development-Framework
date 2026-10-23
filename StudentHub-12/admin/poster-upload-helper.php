<?php
/** Validate and store an optional event poster. Returns relative path or null. */
function studenthub_store_event_poster(array $file, array &$errors): ?string {
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) { $errors[] = 'Poster upload failed. Please try again.'; return null; }
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) { $errors[] = 'Poster must be 2 MB or smaller.'; return null; }
    $tmp = (string)($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) { $errors[] = 'Invalid uploaded file.'; return null; }
    $info = @getimagesize($tmp);
    $mime = is_array($info) ? ($info['mime'] ?? '') : '';
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) { $errors[] = 'Invalid poster type. Upload a JPG, PNG or WEBP image.'; return null; }
    $dir = dirname(__DIR__) . '/uploads/event-posters';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) { $errors[] = 'Upload folder could not be created.'; return null; }
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($tmp, $dir . '/' . $name)) { $errors[] = 'Could not save the poster. Check folder permissions.'; return null; }
    return 'uploads/event-posters/' . $name;
}
function studenthub_delete_event_poster(?string $path): void {
    if (!$path || !preg_match('~^uploads/event-posters/[a-f0-9]{32}\.(jpg|png|webp)$~', $path)) return;
    $full = dirname(__DIR__) . '/' . $path;
    if (is_file($full)) @unlink($full);
}
