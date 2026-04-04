<?php
require_once dirname(__DIR__, 2) . '/includes/admin-auth.php';
bms_require_login();
require_once dirname(__DIR__, 2) . '/includes/db.php';

$uid = bms_current_user_id();

function bms_trash_assert_post_access(PDO $pdo, int $postId, int $userId): bool
{
    if (bms_can_edit_all_posts()) {
        return true;
    }
    $q = $pdo->prepare('SELECT user_id FROM posts WHERE id = ?');
    $q->execute([$postId]);
    $row = $q->fetch(PDO::FETCH_ASSOC);

    return $row && (int) $row['user_id'] === $userId;
}

/* =====================
   MOVE TO TRASH
===================== */
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (!bms_trash_assert_post_access($pdo, $id, $uid)) {
        header('Location: index.php');
        exit;
    }
    $stmt = $pdo->prepare("UPDATE posts SET status='trash' WHERE id=?");
    $stmt->execute([$id]);
    header('Location: index.php');
    exit;
}

/* =====================
   RESTORE POST
===================== */
if (isset($_GET['restore'])) {
    $id = (int)$_GET['restore'];
    if (!bms_trash_assert_post_access($pdo, $id, $uid)) {
        header('Location: trash.php');
        exit;
    }
    $stmt = $pdo->prepare("UPDATE posts SET status='draft' WHERE id=?");
    $stmt->execute([$id]);
    header('Location: trash.php');
    exit;
}

/* =====================
   DELETE PERMANENT
===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (!bms_trash_assert_post_access($pdo, $id, $uid)) {
        header('Location: trash.php');
        exit;
    }

    $pdo->prepare("DELETE FROM post_categories WHERE post_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM post_tags WHERE post_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM posts WHERE id=?")->execute([$id]);

    header('Location: trash.php');
    exit;
}

/* =====================
   FETCH TRASH POSTS
===================== */
if (bms_can_edit_all_posts()) {
    $stmt = $pdo->query("
        SELECT posts.id, posts.title, posts.created_at, users.username
        FROM posts
        LEFT JOIN users ON users.id = posts.user_id
        WHERE posts.status='trash'
        ORDER BY posts.created_at DESC
    ");
    $posts = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("
        SELECT posts.id, posts.title, posts.created_at, users.username
        FROM posts
        LEFT JOIN users ON users.id = posts.user_id
        WHERE posts.status='trash' AND posts.user_id = ?
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute([$uid]);
    $posts = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Trash - WordPress Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
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
<h1 class="wp-heading-inline">Trash</h1>
<a href="index.php" class="page-title-action">← Back to Posts</a>
<hr class="wp-header-end">

<div class="mt-4">
<?php if (!$posts): ?>
    <div class="notice notice-warning">
        <p>Trash is empty.</p>
    </div>
<?php else: ?>

<table class="wp-list-table">
<thead>
<tr>
<th style="width:40%">Title</th>
<th style="width:20%">Author</th>
<th style="width:20%">Date</th>
</tr>
</thead>
<tbody>

<?php foreach ($posts as $post): ?>
<tr>
<td>
    <strong><?=htmlspecialchars($post['title'])?></strong>
    <div class="row-actions">
        <span>
            <a href="?restore=<?=$post['id']?>" 
               onclick="return confirm('Restore this post?')">
               Restore
            </a>
        </span>
        <span class="delete">
            <a href="?delete=<?=$post['id']?>" 
               onclick="return confirm('Delete permanently? This cannot be undone.')">
               Delete Permanently
            </a>
        </span>
    </div>
</td>
<td><?=htmlspecialchars($post['username'])?></td>
<td><?=date('d M Y', strtotime($post['created_at']))?></td>
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
</body>
</html>
