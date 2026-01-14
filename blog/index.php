<?php
require_once __DIR__ . '/includes/db.php';
$config = require __DIR__ . '/includes/config.php';

// Pagination logic
$perPage = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get total published posts
$totalPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$totalPages = ceil($totalPosts / $perPage);

// Fetch paginated posts
$stmt = $pdo->prepare("
    SELECT p.id, p.title, p.slug, p.created_at, p.meta_description, p.featured_image, u.username
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.status='published'
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Latest blog posts">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/includes/header.php'; ?>
    <!-- Banner Section -->
    <section class="banner-section mb-4" style="background: url('<?= htmlspecialchars($config['banner_image']) ?>') center/cover no-repeat; min-height: 260px; display: flex; align-items: center;">
        <div class="container py-5">
            <div class="text-white bg-dark bg-opacity-50 p-4 rounded-3" >
                <h1 class="display-5 fw-bold mb-2"><?= htmlspecialchars($config['site_name']) ?></h1>
                <p class="lead mb-0">Insights, stories, and resources—crafted for you.</p>
            </div>
        </div>
    </section>
    <div class="container">
        <div class="row gy-5 gx-lg-5">
            <main class="col-lg-8">
                <section class="mb-5">
                    <h2 class="mb-4 fw-bold border-bottom pb-2 text-dark">Latest Posts</h2>
                    <div class="row g-4">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-4">
                                <div class="card blog-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                    <a href="<?= $config['base_url'] ?><?= htmlspecialchars($post['slug']) ?>" class="d-block ratio ratio-16x9 bg-light blog-thumb-wrap">
                                        <?php
                                        $imgSrc = (!empty($post['featured_image']))
                                            ? $post['featured_image']
                                            : 'assets/img/default.jpg';
                                        ?>
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" class="object-fit-cover w-100 h-100 blog-thumb" alt="<?= htmlspecialchars($post['title']) ?>">
                                    </a>
                                    <div class="card-body d-flex flex-column p-4">
                                        <h5 class="card-title mb-2 fw-semibold"><a href="<?= $config['base_url'] ?><?= htmlspecialchars($post['slug']) ?>" class="text-decoration-none text-dark blog-title-link"><?=htmlspecialchars($post['title'])?></a></h5>
                                        <div class="mb-2 text-muted small">
                                            <span class="me-2"><i class="bi bi-person"></i> <?=htmlspecialchars($post['username'])?></span>
                                            <span><i class="bi bi-calendar"></i> <?=date('d M Y', strtotime($post['created_at']))?></span>
                                        </div>
                                        <p class="card-text flex-grow-1 text-secondary mb-3"><?=htmlspecialchars($post['meta_description'])?></p>
                                        <a href="<?= $config['base_url'] ?><?= htmlspecialchars($post['slug']) ?>" class="btn btn-primary sidebar-btn mt-auto px-4">Read More</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Blog pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item<?=($page <= 1 ? ' disabled' : '')?>">
                            <a class="page-link" href="?page=<?=($page-1)?>" tabindex="-1">&laquo; Prev</a>
                        </li>
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        for ($i = $start; $i <= $end; $i++) {
                            $active = $i == $page ? ' active' : '';
                            echo "<li class='page-item$active'><a class='page-link' href='?page=$i'>$i</a></li>";
                        }
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            echo "<li class='page-item'><a class='page-link' href='?page=$totalPages'>$totalPages</a></li>";
                        }
                        ?>
                        <li class="page-item<?=($page >= $totalPages ? ' disabled' : '')?>">
                            <a class="page-link" href="?page=<?=($page+1)?>">Next &raquo;</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </main>
            <aside class="col-lg-4">
                <div class="sidebar bg-white border rounded-4 shadow-sm p-4 sticky-top" style="top: 90px;">
                    <?php require __DIR__ . '/includes/sidebar.php'; ?>
                </div>
            </aside>
        </div>
    </div>
    <?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
