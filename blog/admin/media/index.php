<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../includes/db.php';

$user_id = $_SESSION['admin_id'];
$success = '';
$error = '';

// Create uploads directory if not exists
$uploadsDir = '../../assets/uploads/';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

/* =====================
   Handle File Upload
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    $file = $_FILES['media_file'];
    $fileName = basename($file['name']);
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = mime_content_type($fileTmp);
    
    // Validate file
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    if (!in_array($fileType, $allowed)) {
        $error = 'File type not allowed. Allowed: JPG, PNG, WebP, GIF, PDF, DOC, DOCX';
    } elseif ($fileSize > $maxSize) {
        $error = 'File size exceeds 10MB limit';
    } else {
        // Generate unique filename
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = time() . '_' . uniqid() . '.' . $ext;
        $filePath = $uploadsDir . $newFileName;

        if (move_uploaded_file($fileTmp, $filePath)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO media (user_id, file_name, file_path, mime_type, uploaded_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$user_id, $fileName, 'assets/uploads/' . $newFileName, $fileType]);
                $success = 'File uploaded successfully!';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        } else {
            $error = 'Failed to upload file';
        }
    }
}

/* =====================
   Handle File Delete
===================== */
if (isset($_GET['delete'])) {
    $media_id = (int)$_GET['delete'];
    
    $mediaStmt = $pdo->prepare("SELECT * FROM media WHERE id = ? AND user_id = ?");
    $mediaStmt->execute([$media_id, $user_id]);
    $media = $mediaStmt->fetch();

    if ($media) {
        $filePath = '../../' . $media['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $deleteStmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
        $deleteStmt->execute([$media_id]);
        $success = 'File deleted successfully!';
    }
}

/* =====================
   Fetch All Media
===================== */
$stmt = $pdo->prepare("
    SELECT m.*, u.username 
    FROM media m
    LEFT JOIN users u ON m.user_id = u.id
    ORDER BY m.uploaded_at DESC
");
$stmt->execute();
$mediaFiles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Media - WordPress Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<?php require '../partials/admin-style.php'; ?>
<style>
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    .media-item {
        position: relative;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        background: #f5f5f5;
    }
    .media-preview {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }
    .media-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .media-item:hover .media-overlay {
        opacity: 1;
    }
    .media-overlay a {
        margin: 0 5px;
        padding: 5px 10px;
        background: #2271b1;
        color: white;
        border-radius: 3px;
        text-decoration: none;
        font-size: 12px;
    }
    .media-overlay a:hover {
        background: #135e96;
    }
    .media-item.file-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
    }
</style>
</head>
<body>
<?php require '../partials/header.php'; ?>
<div class="container-fluid">
<div class="row">

<?php require '../partials/sidebar.php'; ?>

<main class="col-md-9 col-lg-10">

<div class="wrap">
    <h1 class="wp-heading-inline">Media Library</h1>
    <hr class="wp-header-end">

    <?php if ($success): ?>
    <div class="notice notice-success">
        <p><strong>Success:</strong> <?= htmlspecialchars($success) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="notice notice-error">
        <p><strong>Error:</strong> <?= htmlspecialchars($error) ?></p>
    </div>
    <?php endif; ?>

    <!-- Upload Form -->
    <div class="wp-card p-4 mb-4">
        <h3 style="font-size:18px;margin-bottom:20px;">Upload New Media</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="media_file" class="form-label">Select File</label>
                <input type="file" class="form-control" id="media_file" name="media_file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx" required>
                <small class="form-text text-muted">
                    Allowed: JPG, PNG, WebP, GIF, PDF, DOC, DOCX (Max 10MB)
                </small>
            </div>
            <button type="submit" class="button-primary">Upload File</button>
        </form>
    </div>

    <!-- Media Grid -->
    <div class="wp-card p-4">
        <h3 style="font-size:18px;margin-bottom:20px;">All Media (<?= count($mediaFiles) ?>)</h3>
        
        <?php if (empty($mediaFiles)): ?>
            <p class="text-muted">No files uploaded yet.</p>
        <?php else: ?>
            <div class="media-grid">
                <?php foreach ($mediaFiles as $file): ?>
                    <?php 
                    $isImage = strpos($file['mime_type'], 'image') === 0;
                    $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
                    ?>
                    <div class="media-item <?= !$isImage ? 'file-icon' : '' ?>">
                        <?php if ($isImage): ?>
                            <img src="../../<?= htmlspecialchars($file['file_path']) ?>" alt="<?= htmlspecialchars($file['file_name']) ?>" class="media-preview">
                        <?php else: ?>
                            <span><?= strtoupper($ext) ?></span>
                        <?php endif; ?>
                        <div class="media-overlay">
                            <a href="javascript:void(0)" onclick="copyToClipboard('../../<?= htmlspecialchars($file['file_path']) ?>')">Copy</a>
                            <a href="?delete=<?= $file['id'] ?>" onclick="return confirm('Delete this file?');">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Media Table -->
            <hr class="my-4">
            <h4 style="font-size:16px;margin-bottom:15px;">Media Details</h4>
            <table class="wp-list-table fixed striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Type</th>
                        <th>Uploaded By</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mediaFiles as $file): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($file['file_name']) ?></strong>
                            <br>
                            <small class="text-muted"><?= htmlspecialchars($file['file_path']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($file['mime_type']) ?></td>
                        <td><?= htmlspecialchars($file['username'] ?? 'N/A') ?></td>
                        <td><?= date('M d, Y g:i A', strtotime($file['uploaded_at'])) ?></td>
                        <td>
                            <a href="?delete=<?= $file['id'] ?>" class="text-danger" onclick="return confirm('Delete this file?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</main>
</div>
</div>

<script>
function copyToClipboard(text) {
    const elem = document.createElement('textarea');
    elem.value = text;
    document.body.appendChild(elem);
    elem.select();
    document.execCommand('copy');
    document.body.removeChild(elem);
    alert('URL copied to clipboard!');
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
