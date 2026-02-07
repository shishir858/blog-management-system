<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../includes/db.php';

$success = '';
$error = '';

/* =====================
   ADD TAG
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);

    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    }

    if (empty($name)) {
        $error = 'Tag name is required';
    } else {
        // Check if slug already exists
        $checkSlug = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
        $checkSlug->execute([$slug]);
        if ($checkSlug->fetch()) {
            $error = 'Tag slug already exists';
        } else {
            $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
            $stmt->execute([$name, $slug]);
            $success = 'Tag added successfully';
        }
    }
}

/* =====================
   UPDATE TAG
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);

    if (empty($name)) {
        $error = 'Tag name is required';
    } else {
        // Check if slug exists for another tag
        $checkSlug = $pdo->prepare("SELECT id FROM tags WHERE slug = ? AND id != ?");
        $checkSlug->execute([$slug, $id]);
        if ($checkSlug->fetch()) {
            $error = 'Tag slug already exists';
        } else {
            $stmt = $pdo->prepare("UPDATE tags SET name=?, slug=? WHERE id=?");
            $stmt->execute([$name, $slug, $id]);
            $success = 'Tag updated successfully';
        }
    }
}

/* =====================
   DELETE TAG
===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check if tag is in use
    $check = $pdo->prepare("SELECT COUNT(*) FROM post_tags WHERE tag_id = ?");
    $check->execute([$id]);
    $count = $check->fetchColumn();
    
    if ($count > 0) {
        $error = "Cannot delete tag. It's being used by $count post(s)";
    } else {
        $stmt = $pdo->prepare("DELETE FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Tag deleted successfully';
    }
}

/* =====================
   FETCH TAGS
===================== */
$stmt = $pdo->query("
    SELECT t.*, COUNT(pt.post_id) as post_count
    FROM tags t
    LEFT JOIN post_tags pt ON t.id = pt.tag_id
    GROUP BY t.id
    ORDER BY t.name ASC
");
$tags = $stmt->fetchAll();

// For editing
$editTag = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM tags WHERE id = ?");
    $stmt->execute([$editId]);
    $editTag = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tags - WordPress Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<?php require '../partials/admin-style.php'; ?>
</head>
<body>
<?php require '../partials/header.php'; ?>
<div class="container-fluid">
<div class="row">

<?php require '../partials/sidebar.php'; ?>

<main class="col-md-9 col-lg-10">
    
<div class="wrap">
    <h1 class="wp-heading-inline">Tags</h1>
    <hr class="wp-header-end">

    <?php if ($success): ?>
    <div class="notice notice-success">
        <p><?= htmlspecialchars($success) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="notice notice-error">
        <p><?= htmlspecialchars($error) ?></p>
    </div>
    <?php endif; ?>

    <div class="row mt-4">
        <!-- ADD/EDIT FORM -->
        <div class="col-lg-4">
            <div class="wp-card p-4">
                <h2><?= $editTag ? 'Edit Tag' : 'Add New Tag' ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editTag ? 'update' : 'add' ?>">
                    <?php if ($editTag): ?>
                    <input type="hidden" name="id" value="<?= $editTag['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= $editTag ? htmlspecialchars($editTag['name']) : '' ?>" 
                               required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-semibold">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" 
                               value="<?= $editTag ? htmlspecialchars($editTag['slug']) : '' ?>">
                        <small class="text-muted">Leave empty to auto-generate</small>
                    </div>

                    <button type="submit" class="button-primary">
                        <?= $editTag ? 'Update Tag' : 'Add New Tag' ?>
                    </button>
                    
                    <?php if ($editTag): ?>
                    <a href="index.php" class="button-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- TAGS LIST -->
        <div class="col-lg-8">
            <table class="wp-list-table">
                <thead>
                    <tr>
                        <th style="width: 40%">Name</th>
                        <th style="width: 40%">Slug</th>
                        <th style="width: 80px">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tags): ?>
                        <?php foreach ($tags as $tag): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($tag['name']) ?></strong>
                                <div class="row-actions">
                                    <span><a href="?edit=<?= $tag['id'] ?>">Edit</a></span>
                                    <span class="delete">
                                        <a href="?delete=<?= $tag['id'] ?>" 
                                           onclick="return confirm('Delete this tag?')">Delete</a>
                                    </span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($tag['slug']) ?></td>
                            <td class="text-center"><?= $tag['post_count'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">No tags found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</main>
</div>
</div>
</body>
</html>
