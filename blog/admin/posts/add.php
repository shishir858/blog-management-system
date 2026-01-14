<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login');
    exit;
}

require_once '../../includes/db.php';

$user_id = $_SESSION['admin_id'];
$error = '';

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$tags = $pdo->query("SELECT id, name FROM tags ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title   = trim($_POST['title']);
    $slug    = trim($_POST['slug'] ?? '');
    $content = $_POST['content'];
    $status  = $_POST['status'];
    
    // Auto-generate slug if not provided
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }
    
    // Sanitize slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');

    $meta_title       = trim($_POST['meta_title']);
    $meta_keywords    = trim($_POST['meta_keywords']);
    $meta_description = trim($_POST['meta_description']);
    $canonical_url    = trim($_POST['canonical_url'] ?? '');
    $index_status     = $_POST['index_status'] ?? 'index';
    $schema_type      = $_POST['schema_type'] ?? 'BlogPosting';
    $schema_organization = trim($_POST['schema_organization'] ?? '');
    $schema_logo      = trim($_POST['schema_logo'] ?? '');

    /* Featured Image */
    $featured_image = null;
    $featured_image_alt = trim($_POST['featured_image_alt'] ?? '');
    if (!empty($_FILES['featured_image']['name'])) {

        $uploadDir = '../../assets/uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time().'_'.basename($_FILES['featured_image']['name']);
        $fileTmp  = $_FILES['featured_image']['tmp_name'];
        $filePath = $uploadDir . $fileName;

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {
            move_uploaded_file($fileTmp, $filePath);
            $featured_image = 'assets/uploads/posts/' . $fileName;
        } else {
            $error = 'Only JPG, PNG, WEBP images allowed';
        }
    }

    if ($title === '' || $content === '') {
        $error = 'Title and Content are required';
    }

    if (!$error) {

        $stmt = $pdo->prepare("
            INSERT INTO posts
            (user_id, title, slug, content, status, published_at,
             meta_title, meta_keywords, meta_description, canonical_url, index_status,
             schema_type, schema_organization, schema_logo,
             featured_image, featured_image_alt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

        $stmt->execute([
            $user_id,
            $title,
            $slug,
            $content,
            $status,
            $published_at,
            $meta_title,
            $meta_keywords,
            $meta_description,
            $canonical_url,
            $index_status,
            $schema_type,
            $schema_organization,
            $schema_logo,
            $featured_image,
            $featured_image_alt
        ]);

        $post_id = $pdo->lastInsertId();

        if (!empty($_POST['categories'])) {
            $catStmt = $pdo->prepare(
                "INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)"
            );
            foreach ($_POST['categories'] as $cid) {
                $catStmt->execute([$post_id, $cid]);
            }
        }

        if (!empty($_POST['tags'])) {
            $tagStmt = $pdo->prepare(
                "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)"
            );
            foreach ($_POST['tags'] as $tid) {
                $tagStmt->execute([$post_id, $tid]);
            }
        }

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add New Post - WordPress Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<?php require '../partials/admin-style.php'; ?>

<!-- ✅ TINYMCE (WORKING CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.0/tinymce.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    tinymce.init({
        selector: '#content',
        height: 420,
        menubar: true,
        plugins: 'lists link image code table media preview',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | code preview',
        
        /* Image Upload Configuration */
        images_upload_url: 'upload-image.php',
        automatic_uploads: true,
        images_reuse_filename: false,
        file_picker_types: 'image',
        
        /* Image Upload Handler */
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', 'upload-image.php');
                
                xhr.upload.onprogress = function(e) {
                    progress(e.loaded / e.total * 100);
                };
                
                xhr.onload = function() {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }
                    
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                    
                    var json = JSON.parse(xhr.responseText);
                    
                    if (!json || typeof json.location != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    
                    resolve(json.location);
                };
                
                xhr.onerror = function() {
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };
                
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                
                xhr.send(formData);
            });
        }
    });
});
</script>
</head>

<body>
<?php require '../partials/header.php'; ?>
<div class="container-fluid">
<div class="row">

<?php require '../partials/sidebar.php'; ?>

<main class="col-md-9 col-lg-10">

<div class="wrap">
<h1 class="wp-heading-inline">Add New Post</h1>
<a href="index.php" class="page-title-action">← Back to Posts</a>
<hr class="wp-header-end">

<?php if ($error): ?>
<div class="notice notice-error">
    <p><?=$error?></p>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<div class="row">

<!-- LEFT -->
<div class="col-lg-8">

<div class="wp-card p-4 mb-3">
<label class="form-label fw-semibold">Post Title</label>
<input type="text" name="title" id="post-title" class="form-control" placeholder="Enter title here" required>
</div>

<div class="wp-card p-4 mb-3">
<label class="form-label fw-semibold">Post Slug (SEO Friendly URL)</label>
<div class="input-group">
<span class="input-group-text">/blog/</span>
<input type="text" name="slug" id="post-slug" class="form-control" placeholder="slug-will-auto-generate">
<button type="button" class="btn btn-outline-secondary" id="slug-toggle" onclick="toggleSlugEdit()">Edit</button>
</div>
<small class="form-text text-muted">Auto-generated from title. Click "Edit" to customize.</small>
</div>

<div class="wp-card p-4 mb-3">
<label class="form-label fw-semibold">Content</label>
<textarea name="content" id="content"></textarea>
</div>

<div class="wp-card p-4">
<h5 style="font-size:16px;margin-bottom:15px;">SEO Settings</h5>

<div class="mb-3">
<label class="form-label">Canonical URL</label>
<input type="url" name="canonical_url" class="form-control" placeholder="https://example.com/your-post-url">
<small class="form-text text-muted">Optional: Add canonical URL to avoid duplicate content issues</small>
</div>

<div class="mb-3">
<label class="form-label">Meta Title</label>
<input type="text" name="meta_title" class="form-control" placeholder="SEO Title">
</div>

<div class="mb-3">
<label class="form-label">Meta Keywords</label>
<input type="text" name="meta_keywords" class="form-control" placeholder="keyword1, keyword2, keyword3">
</div>

<div class="mb-3">
<label class="form-label">Meta Description</label>
<textarea name="meta_description" rows="3" class="form-control" placeholder="Brief description for search engines"></textarea>
</div>

<div class="mb-3">
<label class="form-label">Search Engine Indexing</label>
<select name="index_status" class="form-control">
<option value="index" selected>Index (Allow search engines)</option>
<option value="noindex">NoIndex (Hide from search engines)</option>
</select>
<small class="form-text text-muted">Control whether search engines should index this page</small>
</div>

<hr class="my-4">
<h5 style="font-size:16px;margin-bottom:15px;">Schema Markup (Structured Data)</h5>

<div class="mb-3">
<label class="form-label">Schema Type</label>
<select name="schema_type" class="form-control">
<option value="BlogPosting" selected>Blog Posting</option>
<option value="Article">Article</option>
<option value="NewsArticle">News Article</option>
<option value="TechArticle">Tech Article</option>
</select>
<small class="form-text text-muted">Select the type of content for better search visibility</small>
</div>

<div class="mb-3">
<label class="form-label">Organization/Publisher Name</label>
<input type="text" name="schema_organization" class="form-control" placeholder="Your Company Name">
<small class="form-text text-muted">Name of your organization or website</small>
</div>

<div class="mb-3">
<label class="form-label">Organization Logo URL</label>
<input type="url" name="schema_logo" class="form-control" placeholder="https://example.com/logo.png">
<small class="form-text text-muted">Full URL to your organization's logo (recommended: 600x60px)</small>
</div>
</div>

</div>

<!-- RIGHT -->
<div class="col-lg-4">

<div class="wp-card p-3 mb-3">
<h5 style="font-size:16px;margin-bottom:15px;">Publish</h5>
<div class="mb-3">
<label class="form-label">Status</label>
<select name="status" class="form-control">
<option value="draft">Draft</option>
<option value="published">Published</option>
</select>
</div>
<button type="submit" class="button-primary w-100">Publish Post</button>
</div>

<div class="wp-card p-3 mb-3">
<h5 style="font-size:16px;margin-bottom:15px;">Featured Image</h5>
<input type="file" name="featured_image" class="form-control" accept="image/*">
<div class="mt-2">
<label class="form-label small">Alt Text (for SEO & Accessibility)</label>
<input type="text" name="featured_image_alt" class="form-control form-control-sm" placeholder="Describe the image">
</div>
<p class="description mt-2">JPG, PNG or WEBP format</p>
</div>

<div class="wp-card p-3 mb-3">
<h5 style="font-size:16px;margin-bottom:15px;">Categories</h5>
<div style="max-height:200px;overflow-y:auto;">
<?php foreach ($categories as $cat): ?>
<div class="form-check mb-2">
<input class="form-check-input" type="checkbox" name="categories[]" value="<?=$cat['id']?>" id="cat<?=$cat['id']?>">
<label class="form-check-label" for="cat<?=$cat['id']?>"><?=htmlspecialchars($cat['name'])?></label>
</div>
<?php endforeach ?>
</div>
</div>

<div class="wp-card p-3">
<h5 style="font-size:16px;margin-bottom:15px;">Tags</h5>
<div style="max-height:200px;overflow-y:auto;">
<?php foreach ($tags as $tag): ?>
<div class="form-check mb-2">
<input class="form-check-input" type="checkbox" name="tags[]" value="<?=$tag['id']?>" id="tag<?=$tag['id']?>">
<label class="form-check-label" for="tag<?=$tag['id']?>"><?=htmlspecialchars($tag['name'])?></label>
</div>
<?php endforeach ?>
</div>
</div>

</div>
</div>
</form>

</div>

</main>
</div>
</div>

<script>
// Auto-generate slug from title
document.getElementById('post-title').addEventListener('input', function() {
    const title = this.value;
    const slugInput = document.getElementById('post-slug');
    
    // Only auto-generate if slug field is empty or not being edited
    if (!slugInput.dataset.editing || slugInput.dataset.editing === 'false') {
        const slug = title
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        slugInput.value = slug;
    }
});

// Toggle slug edit mode
function toggleSlugEdit() {
    const slugInput = document.getElementById('post-slug');
    const toggleBtn = document.getElementById('slug-toggle');
    
    if (slugInput.dataset.editing === 'true') {
        slugInput.readOnly = true;
        slugInput.dataset.editing = 'false';
        toggleBtn.textContent = 'Edit';
        toggleBtn.classList.remove('btn-secondary');
        toggleBtn.classList.add('btn-outline-secondary');
    } else {
        slugInput.readOnly = false;
        slugInput.dataset.editing = 'true';
        slugInput.focus();
        toggleBtn.textContent = 'Done';
        toggleBtn.classList.remove('btn-outline-secondary');
        toggleBtn.classList.add('btn-secondary');
    }
}

// Sanitize slug as user types
document.getElementById('post-slug').addEventListener('input', function() {
    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9-]/g, '')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    this.value = slug;
});

// Initialize slug field
document.addEventListener('DOMContentLoaded', function() {
    const slugInput = document.getElementById('post-slug');
    slugInput.readOnly = true;
    slugInput.dataset.editing = 'false';
});
</script>

</body>
</html>
