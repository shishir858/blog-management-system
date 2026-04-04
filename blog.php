<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog-functions.php';

$pdo = blog_get_pdo();
$posts = [];
$totalPosts = 0;
$totalPages = 1;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$perPage = 6;
$dbError = '';

if ($pdo) {
    try {
        $totalPosts = (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
        $totalPages = max(1, (int) ceil($totalPosts / $perPage));
        $offset = ($page - 1) * $perPage;

        $stmt = $pdo->prepare("
            SELECT p.id, p.title, p.slug, p.excerpt, p.created_at, p.published_at, p.featured_image, p.meta_description,
                   (SELECT c.name FROM categories c JOIN post_categories pc ON c.id=pc.category_id WHERE pc.post_id=p.id LIMIT 1) AS category
            FROM posts p
            WHERE p.status = 'published'
            ORDER BY COALESCE(p.published_at, p.created_at) DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();
    } catch (Exception $e) {
        $dbError = $e->getMessage();
    }
}

$config = require __DIR__ . '/includes/config.php';
$blogSlugBase = rtrim(bms_url('blog'), '/') . '/';
$page_title = 'Blog · ' . $config['site_name'];
$page_description = 'Articles and updates from ' . $config['site_name'] . '.';
$page_canonical = bms_full_url('blog' . ($page > 1 ? '?page=' . $page : ''));

$bms_stylesheets = ['assets/css/blog-list.css'];
$ubs_extra_classes = 'ubs-blog-list-page';

require __DIR__ . '/includes/portal-open.php';
?>

        <section class="ubs-page-hero">
            <div class="container">
                <h1>Blog</h1>
                <ul class="ubs-breadcrumb">
                    <li><a href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>">Home</a></li>
                    <li><span aria-current="page">Blog</span></li>
                </ul>
            </div>
        </section>

        <section class="ubs-blog-list">
            <div class="container">
                <?php if ($dbError !== ''): ?>
                    <div class="ubs-empty">
                        <p class="mb-3">Blog is temporarily unavailable.</p>
                        <a class="btn ubs-btn" href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>">Back home</a>
                    </div>
                <?php elseif ($posts === []): ?>
                    <div class="ubs-empty">
                        <p class="mb-3">No posts yet. Add one from the admin panel.</p>
                        <a class="btn ubs-btn" href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>">Back home</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($posts as $post): ?>
                            <?php
                            $postDate = $post['published_at'] ?? $post['created_at'];
                            $dateFormatted = date('M j, Y', strtotime((string) $postDate));
                            $excerpt = $post['excerpt'] ?: $post['meta_description'] ?: 'Read more…';
                            if (strlen($excerpt) > 160) {
                                $excerpt = substr($excerpt, 0, 157) . '…';
                            }
                            $imgRel = ltrim((string) ($post['featured_image'] ?? ''), '/');
                            $absFile = $imgRel !== '' ? __DIR__ . '/' . $imgRel : '';
                            $hasImg = $imgRel !== '' && is_file($absFile);
                            $category = $post['category'] ?: 'Article';
                            $readText = blog_est_read_time(($post['excerpt'] ?? '') . ' ' . ($post['meta_description'] ?? ''));
                            $postHref = htmlspecialchars($blogSlugBase . rawurlencode((string) $post['slug']), ENT_QUOTES, 'UTF-8');
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <article class="ubs-post-card">
                                    <a class="ubs-post-card__media" href="<?= $postHref ?>">
                                        <?php if ($hasImg): ?>
                                            <img src="<?= htmlspecialchars(blog_media_src($imgRel), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $post['title'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?php endif; ?>
                                    </a>
                                    <div class="ubs-post-card__body">
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="ubs-badge"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="ubs-badge ubs-badge--muted"><?= htmlspecialchars($dateFormatted, ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="ubs-badge ubs-badge--muted"><?= htmlspecialchars($readText, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <h2><a href="<?= $postHref ?>"><?= htmlspecialchars((string) $post['title'], ENT_QUOTES, 'UTF-8') ?></a></h2>
                                        <p class="ubs-post-card__excerpt"><?= htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                    <div class="ubs-post-card__foot">
                                        <a class="btn ubs-btn w-100" href="<?= $postHref ?>">Read article</a>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="d-flex justify-content-center align-items-center gap-3 flex-wrap mt-5" aria-label="Blog pagination">
                            <?php if ($page > 1): ?>
                                <a class="btn ubs-btn ubs-btn--ghost" href="<?= htmlspecialchars(bms_url('blog') . '?page=' . ($page - 1), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
                            <?php endif; ?>
                            <span class="text-secondary small">Page <?= (int) $page ?> of <?= (int) $totalPages ?></span>
                            <?php if ($page < $totalPages): ?>
                                <a class="btn ubs-btn" href="<?= htmlspecialchars(bms_url('blog') . '?page=' . ($page + 1), ENT_QUOTES, 'UTF-8') ?>">Next</a>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

<?php
require __DIR__ . '/includes/portal-close.php';
