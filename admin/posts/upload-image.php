<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__, 2) . '/includes/admin-auth.php';
bms_require_login();

require_once dirname(__DIR__, 2) . '/includes/db.php';
require_once dirname(__DIR__, 2) . '/includes/config.php';

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
    $msg = ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) ? 'File too large (max 5MB)' : 'Upload error (code ' . $err . ')';
    http_response_code(400);
    echo json_encode(['error' => $msg]);
    exit;
}

$file = $_FILES['file'];
$projectRoot = dirname(__DIR__, 2);
$uploadDir = $projectRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'editor' . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileType = @mime_content_type($file['tmp_name']);
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if ((!$fileType || !in_array($fileType, $allowedTypes)) && !in_array($ext, $allowedExt, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, GIF, WebP allowed.']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max 5MB allowed.']);
    exit;
}

$newFileName = time() . '_' . uniqid('', true) . '.' . $ext;
$filePath = $uploadDir . $newFileName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload file']);
    exit;
}

$relPath = 'assets/uploads/editor/' . $newFileName;
$userId = bms_current_user_id();

try {
    $ins = $pdo->prepare('
        INSERT INTO media (user_id, file_name, file_path, mime_type, uploaded_at)
        VALUES (?, ?, ?, ?, NOW())
    ');
    $ins->execute([$userId, basename($file['name']), $relPath, $fileType ?: 'image/jpeg']);
} catch (Throwable $e) {
    @unlink($filePath);
    http_response_code(500);
    echo json_encode(['error' => 'Could not save to media library']);
    exit;
}

$imgInfo = @getimagesize($filePath);
$fileUrl = rtrim(BASE_URL, '/') . '/' . $relPath;

echo json_encode([
    'location' => $fileUrl,
    'width' => $imgInfo ? $imgInfo[0] : null,
    'height' => $imgInfo ? $imgInfo[1] : null,
]);
