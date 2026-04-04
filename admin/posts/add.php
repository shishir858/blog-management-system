<?php
require_once dirname(__DIR__, 2) . '/includes/admin-auth.php';
bms_require_login();
require_once dirname(__DIR__, 2) . '/includes/db.php';

$user_id = bms_current_user_id();
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

    // Check if slug already exists (excluding trash)
    $checkSlug = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND status != 'trash'");
    $checkSlug->execute([$slug]);
    if ($checkSlug->fetch()) {
        $error = 'Slug already exists! Please use a different slug.';
    }

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
    $featured_media_id = isset($_POST['featured_media_id']) ? (int)$_POST['featured_media_id'] : 0;

    if (!empty($_FILES['featured_image']['name'])) {

        $projectRoot = dirname(dirname(__DIR__));
        $uploadDir = $projectRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time().'_'.basename($_FILES['featured_image']['name']);
        $fileTmp  = $_FILES['featured_image']['tmp_name'];
        $filePath = $uploadDir . $fileName;

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($fileTmp, $filePath)) {
                $featured_image = 'assets/uploads/posts/' . $fileName;
                $mime = @mime_content_type($filePath) ?: 'image/jpeg';
                try {
                    $pdo->prepare('
                        INSERT INTO media (user_id, file_name, file_path, mime_type, uploaded_at)
                        VALUES (?, ?, ?, ?, NOW())
                    ')->execute([$user_id, $fileName, $featured_image, $mime]);
                } catch (Throwable $e) {
                    /* ignore */
                }
            }
        } else {
            $error = 'Only JPG, PNG, WEBP images allowed';
        }
    } elseif ($featured_media_id > 0) {
        $mst = $pdo->prepare("SELECT file_path FROM media WHERE id = ? AND mime_type LIKE 'image/%' LIMIT 1");
        $mst->execute([$featured_media_id]);
        $mrow = $mst->fetch(PDO::FETCH_ASSOC);
        if ($mrow && preg_match('#^assets/uploads/#', (string) $mrow['file_path'])) {
            $featured_image = $mrow['file_path'];
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
<?php
$upload_image_url = bms_full_url('admin/posts/upload-image.php');
$editor_base_url = rtrim(BASE_URL, '/') . '/';
?>
<!-- ✅ TINYMCE (WORKING CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.0/tinymce.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    tinymce.init({
        selector: '#content',
        width: '100%',
        height: 560,
        min_height: 480,
        resize: true,
        menubar: true,
        plugins: 'lists link image code table media preview fullscreen autoresize',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | code preview | fullscreen',
        autoresize_bottom_margin: 24,
        autoresize_min_height: 480,
        autoresize_max_height: 1200,
        images_upload_url: '<?= htmlspecialchars($upload_image_url) ?>',
        images_upload_credentials: true,
        automatic_uploads: true,
        images_reuse_filename: false,
        file_picker_types: 'image',
        convert_urls: true,
        relative_urls: false,
        remove_script_host: false,
        document_base_url: '<?= htmlspecialchars($editor_base_url) ?>',
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image' && typeof window.bmsOpenMediaPicker === 'function') {
                window.bmsOpenMediaPicker(function (it) {
                    callback(it.url, { alt: it.file_name || '' });
                });
            }
        },
        /* Image Upload Handler */
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;
                xhr.open('POST', '<?= htmlspecialchars($upload_image_url) ?>');
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
<?php
$bms_media_list_url = bms_full_url('admin/media/list-json.php');
require dirname(__DIR__) . '/partials/media-picker.php';
?>
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

<!-- Main column: title, slug, editor full width -->
<div class="col-12">

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

<div class="wp-card p-4 mb-3 bms-editor-full">
<label class="form-label fw-semibold">Content</label>
<textarea name="content" id="content"></textarea>
</div>

</div>
</div>
<div class="row g-3 align-items-start">
<div class="col-lg-8">

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
<input type="hidden" name="featured_media_id" id="featured_media_id" value="">
<div id="featured-preview-wrap" class="mb-2"></div>
<button type="button" class="btn btn-outline-primary btn-sm mb-2" id="btn-featured-media">Choose from Media Library</button>
<button type="button" class="btn btn-outline-secondary btn-sm mb-2" id="btn-featured-clear" style="display:none;">Clear selection</button>
<input type="file" name="featured_image" id="featured_file" class="form-control" accept="image/*">
<div class="mt-2">
<label class="form-label small">Alt Text (for SEO & Accessibility)</label>
<input type="text" name="featured_image_alt" class="form-control form-control-sm" placeholder="Describe the image">
</div>
<p class="description mt-2">Upload a new file or pick from Media. JPG, PNG or WEBP.</p>
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

(function () {
    var btnPick = document.getElementById('btn-featured-media');
    var btnClr = document.getElementById('btn-featured-clear');
    var wrap = document.getElementById('featured-preview-wrap');
    var hid = document.getElementById('featured_media_id');
    var fileIn = document.getElementById('featured_file');
    if (btnPick && typeof window.bmsOpenMediaPicker === 'function') {
        btnPick.addEventListener('click', function () {
            window.bmsOpenMediaPicker(function (it) {
                hid.value = String(it.id);
                wrap.innerHTML = '<img src="' + it.url + '" class="img-fluid rounded" alt="">';
                btnClr.style.display = 'inline-block';
                if (fileIn) fileIn.value = '';
            });
        });
    }
    if (btnClr) {
        btnClr.addEventListener('click', function () {
            hid.value = '';
            wrap.innerHTML = '';
            btnClr.style.display = 'none';
            if (fileIn) fileIn.value = '';
        });
    }
    if (fileIn) {
        fileIn.addEventListener('change', function () {
            if (this.files && this.files.length) {
                hid.value = '';
            }
        });
    }
})();
</script>

</body>
</html>
