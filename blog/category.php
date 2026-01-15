<?php
require_once 'includes/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    http_response_code(404);
    echo 'Category not found.';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    http_response_code(404);
    echo 'Category not found.';
    exit;
}

// Fetch posts in this category
$posts = $pdo->prepare("SELECT p.id, p.title, p.slug, p.created_at, p.meta_description FROM posts p JOIN post_categories pc ON p.id=pc.post_id WHERE pc.category_id=? AND p.status='published' ORDER BY p.created_at DESC");
$posts->execute([$category['id']]);
$postsArr = $posts->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=htmlspecialchars($category['name'])?> - Blog Category</title>
    <meta name="description" content="<?=htmlspecialchars($category['description'])?>">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
        <?php require __DIR__ . '/includes/header.php'; ?>
        <script>
        // Ensure header uses bg-white for consistent background
        document.addEventListener('DOMContentLoaded', function() {
            var nav = document.querySelector('nav.navbar');
            if(nav && !nav.classList.contains('bg-white')) nav.classList.add('bg-white');
        });
        </script>
    <!-- Banner -->
    <section class="blog-banner mb-0 position-relative" style="background: url('../assets/img/banner.png') center center/cover no-repeat; min-height: 260px;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.45);"></div>
        <div class="container position-absolute top-50 start-50 translate-middle" style="z-index:2;">
            <h1 class="display-5 fw-bold text-white text-center" style="text-shadow:0 2px 8px rgba(0,0,0,0.4);background:rgba(0,0,0,0.25);display:inline-block;padding:0.5em 1.5em;border-radius:0.5em;">
                <?=htmlspecialchars($category['name'])?>
            </h1>
        </div>
    </section>
    <div class="container mt-5">
        <div class="row gy-5 gx-lg-5">
            <main class="col-lg-8">
                <div class="bg-white p-md-5 rounded-4 shadow-sm mb-4 border-0">
                    <h2 class="fw-bold mb-4">Posts in "<?=htmlspecialchars($category['name'])?>"</h2>
                    <?php if ($postsArr): ?>
                        <ul class="list-unstyled">
                            <?php foreach ($postsArr as $post): ?>
                                <li class="mb-4 pb-4 border-bottom">
                                    <a href="/blog/<?=htmlspecialchars($post['slug'])?>" class="h5 text-decoration-none text-dark fw-semibold">
                                        <?=htmlspecialchars($post['title'])?>
                                    </a>
                                    <div class="text-muted small mb-2">
                                        <i class="bi bi-calendar"></i> <?=date('d M Y', strtotime($post['created_at']))?>
                                    </div>
                                    <p><?=htmlspecialchars($post['meta_description'])?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info">No posts found in this category.</div>
                    <?php endif; ?>
                </div>
            </main>
            <aside class="col-lg-4">
                <div class="sidebar bg-white border rounded-4 shadow-sm p-4 sticky-top" style="top: 90px;">
                    <?php require __DIR__ . '/includes/sidebar.php'; ?>
                </div>
            </aside>
        </div>
    </div>
    <?php require __DIR__ . '/includes/footer.php'; ?>
