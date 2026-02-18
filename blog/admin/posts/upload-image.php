<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// TinyMCE Image Upload Handler
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Upload directory
    $uploadDir = '../../assets/uploads/tinymce/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, GIF, WebP allowed.']);
        exit;
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'File too large. Max 5MB allowed.']);
        exit;
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = time() . '_' . uniqid() . '.' . $ext;
    $filePath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // For TinyMCE admin: return absolute URL and image dimensions (SEO friendly)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $basePath = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/'); // /blog-management-system/blog
        $fileUrl = $protocol . $host . $basePath . '/assets/uploads/tinymce/' . $newFileName;
        $imgInfo = @getimagesize($filePath);
        $width = $imgInfo ? $imgInfo[0] : null;
        $height = $imgInfo ? $imgInfo[1] : null;
        echo json_encode([
            'location' => $fileUrl,
            'width' => $width,
            'height' => $height
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload file']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
}
