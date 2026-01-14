<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db.php';

$admin_name = $_SESSION['admin_name'] ?? 'Admin';

/* =======================
   Counters
======================= */
$totalPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status != 'trash'")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalTags = $pdo->query("SELECT COUNT(*) FROM tags")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$publishedPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$draftPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();

/* =======================
   Recent Posts
======================= */
$stmt = $pdo->query("
    SELECT p.id, p.title, p.status, p.created_at, u.username
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.status != 'trash'
    ORDER BY p.created_at DESC
    LIMIT 10
");
$posts = $stmt->fetchAll();
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

<!-- SIDEBAR -->
<nav class="col-md-3 col-lg-2 sidebar d-md-block" id="sidebarMenu">
    <div class="sidebar-header">
        <div class="text-center py-3">
            <div class="sidebar-logo">UB</div>
            <div class="sidebar-title">Admin Panel</div>
        </div>
    </div>
    <div class="sidebar-menu">
        <a class="active" href="dashboard.php">
            <svg width="20" height="20" fill="currentColor" class="me-2">
                <path d="M3 3h6v8H3V3zm8 0h6v5h-6V3zM3 13h6v5H3v-5zm8-2h6v8h-6v-8z"/>
            </svg>
            Dashboard
        </a>
        <a href="posts/">
            <svg width="20" height="20" fill="currentColor" class="me-2">
                <path d="M3 3h14a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4a1 1 0 011-1zm1 2v10h12V5H4zm2 2h8v2H6V7zm0 4h8v2H6v-2z"/>
            </svg>
            Posts
        </a>
        <a href="categories/">
            <svg width="20" height="20" fill="currentColor" class="me-2">
                <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
            </svg>
            Categories
        </a>
        <a href="tags/">
            <svg width="20" height="20" fill="currentColor" class="me-2">
                <path d="M3 3h8l6 6-6 6-6-6V3zm4 5a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>
            Tags
        </a>
        <a href="users/">
            <svg width="20" height="20" fill="currentColor" class="me-2">
                <path d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0H3z"/>
            </svg>
            Users
        </a>
		<a href="../media/">
            <svg width="20" height="20" fill="currentColor" class="me-2">
                <path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 2h12v8l-2.5-2.5a2 2 0 00-2.83 0L8 13.17l-2.5-2.5a2 2 0 00-2.83 0L4 13v-9zm2 4a2 2 0 100-4 2 2 0 000 4z"/>
            </svg>
            Media
        </a>
        <a href="settings/">
            <svg width="20" height="20" fill="currentColor" class="me-2">
                <path d="M10 2a2 2 0 00-2 2v1.17a6 6 0 00-1.73 1l-1-.58a2 2 0 00-2.73.73l-1 1.73a2 2 0 00.73 2.73l1 .58a6 6 0 000 2l-1 .58a2 2 0 00-.73 2.73l1 1.73a2 2 0 002.73.73l1-.58a6 6 0 001.73 1V16a2 2 0 002 2h2a2 2 0 002-2v-1.17a6 6 0 001.73-1l1 .58a2 2 0 002.73-.73l1-1.73a2 2 0 00-.73-2.73l-1-.58a6 6 0 000-2l1-.58a2 2 0 00.73-2.73l-1-1.73a2 2 0 00-2.73-.73l-1 .58a6 6 0 00-1.73-1V4a2 2 0 00-2-2h-2zm0 6a4 4 0 110 8 4 4 0 010-8z"/>
            </svg>
            Settings
        </a>
    </div>
</nav>

<!-- MAIN -->
<main class="col-md-9 col-lg-10">

<div class="wrap">
<h1 class="wp-heading-inline">Dashboard</h1>
<hr class="wp-header-end">

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
<div class="col-md-3">
    <div class="stats-card">
        <h3>Total Users</h3>
        <p class="count" style="color:#d63638;"><?=$totalUsers?></p>
    </div>
</div>
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
