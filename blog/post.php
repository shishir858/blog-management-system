

<?php
require_once __DIR__ . '/includes/db.php';
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    http_response_code(404);
    echo 'Post not found.';
    exit;
}
$stmt = $pdo->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.slug = ? AND p.status = 'published' LIMIT 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    echo 'Post not found.';
    exit;
}
$cats = $pdo->prepare("SELECT c.name, c.slug FROM categories c JOIN post_categories pc ON c.id=pc.category_id WHERE pc.post_id=?");
$cats->execute([$post['id']]);
$categories = $cats->fetchAll();
$tags = $pdo->prepare("SELECT t.name, t.slug FROM tags t JOIN post_tags pt ON t.id=pt.tag_id WHERE pt.post_id=?");
$tags->execute([$post['id']]);
$tagsArr = $tags->fetchAll();
// Related posts (same category, exclude current)
$related = $pdo->prepare("SELECT p.title, p.slug, p.featured_image FROM posts p JOIN post_categories pc ON p.id=pc.post_id WHERE pc.category_id=? AND p.id!=? AND p.status='published' ORDER BY p.created_at DESC LIMIT 3");
$related->execute([$categories[0]['slug'] ?? 0, $post['id']]);
$relatedPosts = $related->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=htmlspecialchars($post['meta_title'] ?: $post['title'])?></title>
    <meta name="description" content="<?=htmlspecialchars($post['meta_description'])?>">
    <?php if (!empty($post['meta_keywords'])): ?>
        <meta name="keywords" content="<?=htmlspecialchars($post['meta_keywords'])?>">
    <?php endif; ?>
    <link rel="canonical" href="<?php
        if (!empty($post['canonical_url'])) {
            echo htmlspecialchars($post['canonical_url']);
        } else {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $base = rtrim($config['base_url'], '/');
            echo $protocol . $host . $base . '/' . htmlspecialchars($post['slug']);
        }
    ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php require __DIR__ . '/includes/header.php'; ?>
    <!-- Banner -->
    <?php
    $imgSrc = (!empty($post['featured_image']))
        ? $post['featured_image']
        : 'assets/img/default.jpg';
    ?>
    <section class="blog-banner mb-0 position-relative" style="background: #f8f9fa;">
        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?=htmlspecialchars($post['title'])?>" class="w-100" style="max-height:340px;object-fit:cover;">
    </section>
    <!-- Main Title (above meta) -->
    <!-- <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="display-5 fw-bold mb-4 mt-4 text-dark text-center"><?=htmlspecialchars($post['title'])?></h1>
            </div>
        </div>
    </div> -->
    <!-- Main Section -->
    <div class="container mt-5">
        <div class="row gy-5 gx-lg-5">
            <!-- Left: Post Details & Comments -->
            <main class="col-lg-8">
                    <div class="bg-white p-md-5 rounded-4 shadow-sm mb-4 border-0">
                            <div class="col-12">
                        <h1 class="display-5 fw-bold mb-3 text-dark "><?=htmlspecialchars($post['title'])?></h1>
                    </div>
                    <div class="d-flex flex-wrap gap-3 align-items-center mb-3 text-muted small">
                        <span><i class="bi bi-person"></i> <?=htmlspecialchars($post['username'])?></span>
                        <span><i class="bi bi-calendar"></i> <?=date('d M Y', strtotime($post['created_at']))?></span>
                        <?php if ($categories): ?>
                            <span><i class="bi bi-folder"></i>
                            <?php foreach ($categories as $cat): ?>
                                <a href="/blog/category/<?=htmlspecialchars($cat['slug'])?>" class="text-decoration-none text-primary ms-1"> <?=htmlspecialchars($cat['name'])?></a>
                            <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="single-post-content fs-5 lh-lg mb-4">
                        <?= str_replace('../../assets/uploads/tinymce/', 'assets/uploads/tinymce/', $post['content']) ?>
                    </div>
                    <?php if ($tagsArr): ?>
                        <div class="single-post-tags mt-3">
                            <span class="fw-semibold text-secondary">Tags:</span>
                            <?php foreach ($tagsArr as $tag): ?>
                                <a href="/blog/tag/<?=htmlspecialchars($tag['slug'])?>" class="badge">#<?=htmlspecialchars($tag['name'])?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <!-- Comments Section Placeholder -->
                    <div class="mt-5">
                        <h4 class="mb-3">Comments</h4>
                        <div class="card p-4 border-0 shadow-sm mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle bg-secondary" style="width:40px;height:40px;"></div>
                                <div class="ms-3">
                                    <span class="fw-semibold">Guest</span>
                                    <span class="text-muted small ms-2">Just now</span>
                                </div>
                            </div>
                            <div class="text-muted">Comments system coming soon!</div>
                        </div>
                        <form class="mt-4">
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="Add a comment..." disabled></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit" disabled>Post Comment</button>
                        </form>
                    </div>
                </div>
            </main>
            <!-- Right: Sidebar -->
            <aside class="col-lg-4">
                <div class="sidebar bg-white border rounded-4 shadow-sm p-4 sticky-top" style="top: 90px;">
                    <?php require __DIR__ . '/includes/sidebar.php'; ?>
                </div>
            </aside>
        </div>
    </div>
    <!-- Related Posts -->
    <?php if ($relatedPosts): ?>
    <section class="container mb-5">
        <h3 class="fw-bold mb-4 mt-5 text-dark">Related Posts</h3>
        <div class="row g-4">
            <?php foreach ($relatedPosts as $rel): ?>
                <div class="col-md-4">
                    <div class="card blog-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <a href="<?= $rel['slug'] ?>" class="d-block ratio ratio-16x9 bg-light blog-thumb-wrap">
                            <img src="<?= htmlspecialchars($rel['featured_image']) ?>" class="object-fit-cover w-100 h-100 blog-thumb" alt="<?= htmlspecialchars($rel['title']) ?>">
                        </a>
                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="card-title mb-2 fw-semibold"><a href="<?= $rel['slug'] ?>" class="text-decoration-none text-dark blog-title-link"><?=htmlspecialchars($rel['title'])?></a></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    <?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
