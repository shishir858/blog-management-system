<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login');
    exit;
}

require_once '../../includes/db.php';

/* Fetch Posts (exclude trash) */
$posts = $pdo->query("
    SELECT p.id, p.title, p.status, p.created_at, u.username
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.status != 'trash'
    ORDER BY p.created_at DESC
")->fetchAll();

/* Count trash items */
$trashCount = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='trash'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Posts - WordPress Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<?php require '../partials/admin-style.php'; ?>
</head>

<body>
<?php require '../partials/header.php'; ?>
<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<?php require '../partials/sidebar.php'; ?>

<!-- MAIN CONTENT -->
<main class="col-md-9 col-lg-10">

<div class="wrap">
<!-- HEADER -->
<h1 class="wp-heading-inline">Posts</h1>
<a href="add.php" class="page-title-action">Add New</a>
<hr class="wp-header-end">

<!-- Submenu -->
<ul class="nav nav-tabs mt-3">
    <li class="nav-item">
        <a class="nav-link active" href="index.php">All</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="trash.php">Trash <?= $trashCount > 0 ? "($trashCount)" : '' ?></a>
    </li>
</ul>

<!-- POSTS TABLE -->
<table class="wp-list-table">
<thead>
<tr>
    <th style="width:35%">Title</th>
    <th style="width:15%">Author</th>
    <th style="width:15%">Categories</th>
    <th style="width:10%">Tags</th>
    <th style="width:15%">Date</th>
</tr>
</thead>
<tbody>

<?php if ($posts): foreach ($posts as $post): ?>
<?php
// Fetch categories for this post
$cats = $pdo->prepare("SELECT c.name FROM categories c JOIN post_categories pc ON c.id=pc.category_id WHERE pc.post_id=?");
$cats->execute([$post['id']]);
$categories = $cats->fetchAll(PDO::FETCH_COLUMN);

// Fetch tags count
$tagsCount = $pdo->prepare("SELECT COUNT(*) FROM post_tags WHERE post_id=?");
$tagsCount->execute([$post['id']]);
$tagCount = $tagsCount->fetchColumn();
?>
<tr>
    <td>
        <strong><?=htmlspecialchars($post['title'])?></strong>
        <?php
        $statusColor = match($post['status']){
            'published'=>'success',
            'draft'=>'secondary',
            'archived'=>'warning',
            default=>'dark'
        };
        ?>
        <span class="badge bg-<?=$statusColor?>" style="font-size:11px;"><?=ucfirst($post['status'])?></span>
        <div class="row-actions">
            <span><a href="edit.php?id=<?=$post['id']?>">Edit</a></span>
            <span class="delete"><a href="trash.php?id=<?=$post['id']?>" onclick="return confirm('Move to trash?')">Trash</a></span>
            <?php /* <span><a href="../../post.php?slug=<?=urlencode($post['slug'])?>" target="_blank">View</a></span> */ ?>
        </div>
    </td>
    <td><?=htmlspecialchars($post['username'])?></td>
    <td><?= $categories ? implode(', ', array_map('htmlspecialchars', $categories)) : '—' ?></td>
    <td><?= $tagCount > 0 ? $tagCount : '—' ?></td>
    <td><?=date('d M Y', strtotime($post['created_at']))?></td>
</tr>
<?php endforeach; else: ?>
<tr>
    <td colspan="5" class="text-center text-muted py-4">No posts found</td>
</tr>
<?php endif ?>

</tbody>
</table>

</div>

</main>
</div>
</div>
</body>
</html>
