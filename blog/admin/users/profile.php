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

/* =====================
   Fetch Current User Data
===================== */
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: ../dashboard.php');
    exit;
}

/* =====================
   Update Profile
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $display_name = trim($_POST['display_name']);
    $bio = trim($_POST['bio'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email)) {
        $error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Check if changing password
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $error = 'Current password is required to change password';
            } elseif (!password_verify($current_password, $user['password'])) {
                $error = 'Current password is incorrect';
            } elseif (strlen($new_password) < 6) {
                $error = 'New password must be at least 6 characters';
            } elseif ($new_password !== $confirm_password) {
                $error = 'New passwords do not match';
            } else {
                // Update with new password
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                try {
                    $stmt = $pdo->prepare("UPDATE users SET email=?, display_name=?, password=? WHERE id=?");
                    $stmt->execute([$email, $display_name, $hashedPassword, $user_id]);
                    $_SESSION['admin_name'] = $display_name ?: $user['username'];
                    $success = 'Profile updated successfully with new password';
                    
                    // Refresh user data
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch();
                } catch (PDOException $e) {
                    $error = 'Email already exists';
                }
            }
        } else {
            // Update without password change
            try {
                $stmt = $pdo->prepare("UPDATE users SET email=?, display_name=? WHERE id=?");
                $stmt->execute([$email, $display_name, $user_id]);
                $_SESSION['admin_name'] = $display_name ?: $user['username'];
                $success = 'Profile updated successfully';
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } catch (PDOException $e) {
                $error = 'Email already exists';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile - WordPress Admin</title>
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
    <h1 class="wp-heading-inline">Profile</h1>
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

    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="wp-card p-4">
                <h2 style="font-size:18px;margin-bottom:20px;">Personal Information</h2>
                
                <form method="post">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="username">Username</label></th>
                                <td>
                                    <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" disabled>
                                    <p class="description">Usernames cannot be changed</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="email">Email *</label></th>
                                <td>
                                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="display_name">Display Name</label></th>
                                <td>
                                    <input type="text" name="display_name" id="display_name" value="<?= htmlspecialchars($user['display_name']) ?>" class="form-control">
                                    <p class="description">This is how your name will be displayed</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label>Role</label></th>
                                <td>
                                    <strong><?= ucfirst($user['role']) ?></strong>
                                    <p class="description">Contact another administrator to change your role</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label>Account Created</label></th>
                                <td>
                                    <?= date('F j, Y, g:i a', strtotime($user['created_at'])) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h2 style="font-size:18px;margin:30px 0 20px;padding-top:20px;border-top:1px solid #c3c4c7;">Change Password</h2>
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="current_password">Current Password</label></th>
                                <td>
                                    <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="off">
                                    <p class="description">Required only if changing password</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="new_password">New Password</label></th>
                                <td>
                                    <input type="password" name="new_password" id="new_password" class="form-control" autocomplete="new-password">
                                    <p class="description">Leave blank to keep current password</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="confirm_password">Confirm New Password</label></th>
                                <td>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="new-password">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit" style="margin-top:30px;">
                        <button type="submit" class="button-primary">Update Profile</button>
                        <a href="../dashboard.php" class="button-secondary">Cancel</a>
                    </p>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="wp-card p-4">
                <h3 style="font-size:16px;margin-bottom:15px;">Profile Picture</h3>
                <div class="text-center">
                    <div class="user-avatar" style="width:120px;height:120px;font-size:48px;margin:20px auto;">
                        <?= strtoupper(substr($user['display_name'] ?: $user['username'], 0, 1)) ?>
                    </div>
                    <p class="description">Avatar based on your name</p>
                </div>
            </div>

            <div class="wp-card p-4 mt-3">
                <h3 style="font-size:16px;margin-bottom:15px;">Account Stats</h3>
                <?php
                $postCount = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
                $postCount->execute([$user_id]);
                $totalPosts = $postCount->fetchColumn();

                $publishedCount = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'published'");
                $publishedCount->execute([$user_id]);
                $published = $publishedCount->fetchColumn();
                ?>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>Total Posts:</strong> <?= $totalPosts ?>
                    </li>
                    <li class="mb-2">
                        <strong>Published:</strong> <?= $published ?>
                    </li>
                    <li class="mb-2">
                        <strong>Drafts:</strong> <?= $totalPosts - $published ?>
                    </li>
                </ul>
            </div>

            <div class="wp-card p-4 mt-3">
                <h3 style="font-size:16px;margin-bottom:15px;">Security Tips</h3>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2">✓ Use a strong password (min 6 characters)</li>
                    <li class="mb-2">✓ Don't share your password</li>
                    <li class="mb-2">✓ Change password regularly</li>
                    <li class="mb-2">✓ Keep your email up to date</li>
                </ul>
            </div>
        </div>
    </div>
</div>

</main>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
