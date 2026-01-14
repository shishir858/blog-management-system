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
   ADD USER
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $display_name = trim($_POST['display_name']);
    $role = $_POST['role'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Username, email and password are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, display_name, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $display_name, $role]);
            $success = 'User added successfully';
        } catch (PDOException $e) {
            $error = 'Username or email already exists';
        }
    }
}

/* =====================
   UPDATE USER
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $display_name = trim($_POST['display_name']);
    $role = $_POST['role'];

    if (empty($username) || empty($email)) {
        $error = 'Username and email are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        try {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=?, display_name=?, role=? WHERE id=?");
                $stmt->execute([$username, $email, $hashedPassword, $display_name, $role, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, display_name=?, role=? WHERE id=?");
                $stmt->execute([$username, $email, $display_name, $role, $id]);
            }
            $success = 'User updated successfully';
        } catch (PDOException $e) {
            $error = 'Username or email already exists';
        }
    }
}

/* =====================
   DELETE USER
===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Prevent deleting self
    if ($id == $_SESSION['admin_id']) {
        $error = "You cannot delete your own account";
    } else {
        // Check if user has posts
        $check = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
        $check->execute([$id]);
        $count = $check->fetchColumn();
        
        if ($count > 0) {
            $error = "Cannot delete user. They have $count post(s). Reassign posts first.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'User deleted successfully';
        }
    }
}

/* =====================
   FETCH USERS
===================== */
$stmt = $pdo->query("
    SELECT u.*, COUNT(p.id) as post_count
    FROM users u
    LEFT JOIN posts p ON u.id = p.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();

// For editing
$editUser = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$editId]);
    $editUser = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users - WordPress Admin</title>
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
    <h1 class="wp-heading-inline">Users</h1>
    <a href="?add" class="page-title-action">Add New</a>
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

    <?php if (isset($_GET['add']) || $editUser): ?>
    <!-- ADD/EDIT FORM -->
    <div class="wp-card p-4 mt-4" style="max-width: 700px;">
        <h2><?= $editUser ? 'Edit User' : 'Add New User' ?></h2>
        <form method="post" class="mt-4">
            <input type="hidden" name="action" value="<?= $editUser ? 'update' : 'add' ?>">
            <?php if ($editUser): ?>
            <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
            <?php endif; ?>

            <table class="form-table">
                <tr>
                    <th><label for="username">Username *</label></th>
                    <td>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= $editUser ? htmlspecialchars($editUser['username']) : '' ?>" 
                               required <?= $editUser ? 'readonly' : '' ?>>
                        <?php if ($editUser): ?>
                        <p class="description">Usernames cannot be changed</p>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th><label for="email">Email *</label></th>
                    <td>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= $editUser ? htmlspecialchars($editUser['email']) : '' ?>" 
                               required>
                    </td>
                </tr>

                <tr>
                    <th><label for="password">Password <?= !$editUser ? '*' : '' ?></label></th>
                    <td>
                        <input type="password" class="form-control" id="password" name="password" 
                               <?= !$editUser ? 'required' : '' ?>>
                        <?php if ($editUser): ?>
                        <p class="description">Leave blank to keep current password</p>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th><label for="display_name">Display Name</label></th>
                    <td>
                        <input type="text" class="form-control" id="display_name" name="display_name" 
                               value="<?= $editUser ? htmlspecialchars($editUser['display_name']) : '' ?>">
                    </td>
                </tr>

                <tr>
                    <th><label for="role">Role *</label></th>
                    <td>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin" <?= ($editUser && $editUser['role'] == 'admin') ? 'selected' : '' ?>>Administrator</option>
                            <option value="editor" <?= ($editUser && $editUser['role'] == 'editor') ? 'selected' : '' ?>>Editor</option>
                            <option value="author" <?= ($editUser && $editUser['role'] == 'author') ? 'selected' : '' ?>>Author</option>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="mt-4">
                <button type="submit" class="button-primary">
                    <?= $editUser ? 'Update User' : 'Add New User' ?>
                </button>
                <a href="index.php" class="button-secondary">Cancel</a>
            </p>
        </form>
    </div>

    <?php else: ?>
    <!-- USERS LIST -->
    <table class="wp-list-table">
        <thead>
            <tr>
                <th style="width: 20%">Username</th>
                <th style="width: 25%">Email</th>
                <th style="width: 20%">Display Name</th>
                <th style="width: 15%">Role</th>
                <th style="width: 10%">Posts</th>
                <th style="width: 10%">Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($user['username']) ?></strong>
                        <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                        <span class="badge bg-primary" style="font-size:11px;">You</span>
                        <?php endif; ?>
                        <div class="row-actions">
                            <span><a href="?edit=<?= $user['id'] ?>">Edit</a></span>
                            <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                            <span class="delete">
                                <a href="?delete=<?= $user['id'] ?>" 
                                   onclick="return confirm('Delete this user?')">Delete</a>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['display_name'] ?: '-') ?></td>
                    <td>
                        <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'editor' ? 'warning' : 'secondary') ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </td>
                    <td class="text-center"><?= $user['post_count'] ?></td>
                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

</main>
</div>
</div>
</body>
</html>
