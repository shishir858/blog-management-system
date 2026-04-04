<?php
require_once dirname(__DIR__) . '/includes/admin-auth.php';
bms_require_login();
require_once dirname(__DIR__) . '/includes/db.php';

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$role = bms_user_role();
$uid = bms_current_user_id();

/* =======================
   Counters
======================= */
if (bms_can_edit_all_posts()) {
    $totalPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status != 'trash'")->fetchColumn();
    $publishedPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
    $draftPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();
} else {
    $q = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE status != 'trash' AND user_id = ?");
    $q->execute([$uid]);
    $totalPosts = $q->fetchColumn();
    $q2 = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE status = 'published' AND user_id = ?");
    $q2->execute([$uid]);
    $publishedPosts = $q2->fetchColumn();
    $q3 = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE status = 'draft' AND user_id = ?");
    $q3->execute([$uid]);
    $draftPosts = $q3->fetchColumn();
}

$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalTags = $pdo->query("SELECT COUNT(*) FROM tags")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

/* =======================
   Recent Posts
======================= */
if (bms_can_edit_all_posts()) {
    $stmt = $pdo->query("
        SELECT p.id, p.title, p.status, p.created_at, u.username
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.status != 'trash'
        ORDER BY p.created_at DESC
        LIMIT 10
    ");
    $posts = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("
        SELECT p.id, p.title, p.status, p.created_at, u.username
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.status != 'trash' AND p.user_id = ?
        ORDER BY p.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$uid]);
    $posts = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - WordPress Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<?php require 'partials/admin-style.php'; ?>
</head>

<body>
<?php require 'partials/header.php'; ?>
<div class="container-fluid">
<div class="row">
<?php require __DIR__ . '/partials/sidebar.php'; ?>

<!-- MAIN -->
<main class="col-md-9 col-lg-10">

<div class="wrap">
<h1 class="wp-heading-inline">Dashboard</h1>
<p class="text-muted mb-0" style="font-size:14px;">Signed in as <strong><?= htmlspecialchars($admin_name) ?></strong> · Role: <strong><?= htmlspecialchars(ucfirst($role)) ?></strong>
<?php if ($role === 'author'): ?> — you can manage your own posts, media, and profile.<?php elseif ($role === 'editor'): ?> — you can manage all posts, categories, tags, and media.<?php endif; ?>
</p>
<hr class="wp-header-end">

<!-- SEO & Sitemap Info -->
<div class="col-md-12 mb-3">
    <div class="alert alert-info" style="display:flex;align-items:center;gap:20px;">
        <div>
            <b>Sitemap:</b> <a href="../sitemap.xml" target="_blank">sitemap.xml</a>
        </div>
        <div>
            <b>Robots.txt:</b> <a href="../robots.txt" target="_blank">View robots.txt</a>
        </div>
        <div>
            <b>Indexing Summary:</b> 
            <?php
            $indexCount = $pdo->query("SELECT COUNT(*) FROM posts WHERE index_status='index' AND status='published'")->fetchColumn();
            $noindexCount = $pdo->query("SELECT COUNT(*) FROM posts WHERE index_status='noindex' AND status='published'")->fetchColumn();
            ?>
            <span class="badge bg-success">Index: <?=$indexCount?></span>
            <span class="badge bg-warning text-dark">NoIndex: <?=$noindexCount?></span>
        </div>
    </div>
</div>

<!-- STATS CARDS -->
<div class="row g-3 mt-3">
<div class="col-md-3">
    <div class="stats-card">
        <h3>Total Posts</h3>
        <p class="count"><?=$totalPosts?></p>
    </div>
</div>
<div class="col-md-3">
    <div class="stats-card">
        <h3>Published</h3>
        <p class="count" style="color:#00a32a;"><?=$publishedPosts?></p>
    </div>
</div>
<div class="col-md-3">
    <div class="stats-card">
        <h3>Drafts</h3>
        <p class="count" style="color:#dba617;"><?=$draftPosts?></p>
    </div>
</div>
<?php if (bms_can_manage_users()): ?>
<div class="col-md-3">
    <div class="stats-card">
        <h3>Total Users</h3>
        <p class="count" style="color:#d63638;"><?=$totalUsers?></p>
    </div>
</div>
<?php endif; ?>
</div>

<!-- Quick Stats Row -->
<div class="row g-3 mt-3">
<div class="col-md-4">
    <div class="stats-card">
        <h3>Categories</h3>
        <p class="count"><?=$totalCategories?></p>
    </div>
</div>
<div class="col-md-4">
    <div class="stats-card">
        <h3>Tags</h3>
        <p class="count"><?=$totalTags?></p>
    </div>
</div>
<div class="col-md-4">
    <div class="stats-card">
        <h3>At a Glance</h3>
        <p style="font-size:14px;margin:10px 0 0;">
            <?=$publishedPosts?> Published<br>
            <?=$draftPosts?> Drafts
        </p>
    </div>
</div>
</div>

<!-- RECENT POSTS -->
<div class="wp-card mt-4">
<div class="p-3" style="border-bottom:1px solid var(--wp-border);">
    <h2 style="font-size:18px;margin:0;">Recent Posts</h2>
</div>

<table class="wp-list-table" style="border:none;margin:0;">
<thead>
<tr>
    <th style="width:40%">Title</th>
    <th style="width:20%">Author</th>
    <th style="width:15%">Status</th>
    <th style="width:15%">Date</th>
</tr>
</thead>
<tbody>
<?php if ($posts): foreach($posts as $row): ?>
<tr>
<td>
    <strong><?=htmlspecialchars($row['title'])?></strong>
    <div class="row-actions">
        <span><a href="posts/edit.php?id=<?=$row['id']?>">Edit</a></span>
        <?php /* <span><a href="../post.php?slug=<?=urlencode($row['slug'])?>" target="_blank">View</a></span> */ ?>
    </div>
</td>
<td><?=htmlspecialchars($row['username'])?></td>
<td>
<span class="badge bg-<?=
$row['status']=='published'?'success':(
$row['status']=='draft'?'secondary':'warning')?>"
style="font-size:11px;">
<?=ucfirst($row['status'])?>
</span>
</td>
<td><?=date("d M Y", strtotime($row['created_at']))?></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="4" class="text-center text-muted py-3">No posts found</td></tr>
<?php endif ?>
</tbody>
</table>
</div>


</main>
</div>
</div>
</body>
</html>
