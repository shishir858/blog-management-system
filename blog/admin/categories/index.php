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
   ADD CATEGORY
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);

    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    }

    if (empty($name)) {
        $error = 'Category name is required';
    } else {
        // Check if slug already exists
        $checkSlug = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $checkSlug->execute([$slug]);
        if ($checkSlug->fetch()) {
            $error = 'Category slug already exists';
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $slug, $description]);
            $success = 'Category added successfully';
        }
    }
}

/* =====================
   UPDATE CATEGORY
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $error = 'Category name is required';
    } else {
        // Check if slug exists for another category
        $checkSlug = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
        $checkSlug->execute([$slug, $id]);
        if ($checkSlug->fetch()) {
            $error = 'Category slug already exists';
        } else {
            $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, description=? WHERE id=?");
            $stmt->execute([$name, $slug, $description, $id]);
            $success = 'Category updated successfully';
        }
    }
}

/* =====================
   DELETE CATEGORY
===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check if category is in use
    $check = $pdo->prepare("SELECT COUNT(*) FROM post_categories WHERE category_id = ?");
    $check->execute([$id]);
    $count = $check->fetchColumn();
    
    if ($count > 0) {
        $error = "Cannot delete category. It's being used by $count post(s)";
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Category deleted successfully';
    }
}

/* =====================
   FETCH CATEGORIES
===================== */
$stmt = $pdo->query("
    SELECT c.*, COUNT(pc.post_id) as post_count
    FROM categories c
    LEFT JOIN post_categories pc ON c.id = pc.category_id
    GROUP BY c.id
    ORDER BY c.name ASC
");
$categories = $stmt->fetchAll();

// For editing
$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$editId]);
    $editCategory = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Categories - WordPress Admin</title>
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
    <h1 class="wp-heading-inline">Categories</h1>
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
                <h2><?= $editCategory ? 'Edit Category' : 'Add New Category' ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editCategory ? 'update' : 'add' ?>">
                    <?php if ($editCategory): ?>
                    <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= $editCategory ? htmlspecialchars($editCategory['name']) : '' ?>" 
                               required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-semibold">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" 
                               value="<?= $editCategory ? htmlspecialchars($editCategory['slug']) : '' ?>">
                        <small class="text-muted">Leave empty to auto-generate</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= $editCategory ? htmlspecialchars($editCategory['description']) : '' ?></textarea>
                    </div>

                    <button type="submit" class="button-primary">
                        <?= $editCategory ? 'Update Category' : 'Add New Category' ?>
                    </button>
                    
                    <?php if ($editCategory): ?>
                    <a href="index.php" class="button-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- CATEGORIES LIST -->
        <div class="col-lg-8">
            <table class="wp-list-table">
                <thead>
                    <tr>
                        <th style="width: 30%">Name</th>
                        <th style="width: 30%">Slug</th>
                        <th>Description</th>
                        <th style="width: 80px">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories): ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($cat['name']) ?></strong>
                                <div class="row-actions">
                                    <span><a href="?edit=<?= $cat['id'] ?>">Edit</a></span>
                                    <span class="delete">
                                        <a href="?delete=<?= $cat['id'] ?>" 
                                           onclick="return confirm('Delete this category?')">Delete</a>
                                    </span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($cat['slug']) ?></td>
                            <td class="text-muted small">
                                <?= $cat['description'] ? htmlspecialchars(substr($cat['description'], 0, 50)) . '...' : '-' ?>
                            </td>
                            <td class="text-center"><?= $cat['post_count'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No categories found</td>
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
