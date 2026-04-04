<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog-functions.php';

$post_slug = $_GET['post'] ?? '';
if ($post_slug === '') {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('#/blog/([^/?#]+)#', $requestUri, $m)) {
        $post_slug = rawurldecode($m[1]);
    }
}

if ($post_slug === '') {
    header('Location: ' . bms_url('blog'));
    exit;
}

if (!empty($_GET['post']) && empty($_GET['from_rewrite'])) {
    $pretty = rtrim(bms_url('blog'), '/') . '/' . rawurlencode($post_slug);
    header('Location: ' . $pretty, true, 301);
    exit;
}

$post = null;
$others = [];
$pdo = blog_get_pdo();

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT p.id, p.title, p.slug, p.content, p.excerpt, p.featured_image, p.featured_image_alt,
                   p.created_at, p.updated_at, p.published_at, p.meta_title, p.meta_keywords, p.meta_description,
                   p.canonical_url, p.index_status, p.schema_type, p.schema_organization, p.schema_logo,
                   (SELECT c.name FROM categories c JOIN post_categories pc ON c.id=pc.category_id WHERE pc.post_id=p.id LIMIT 1) AS category
            FROM posts p
            WHERE p.slug = ? AND p.status = 'published'
        ");
        $stmt->execute([$post_slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            $relatedStmt = $pdo->prepare("
                SELECT p.id, p.title, p.slug, p.excerpt, p.featured_image, p.published_at, p.created_at,
                       (SELECT c.name FROM categories c JOIN post_categories pc ON c.id=pc.category_id WHERE pc.post_id=p.id LIMIT 1) AS category
                FROM posts p
                WHERE p.status = 'published' AND p.id != ?
                ORDER BY COALESCE(p.published_at, p.created_at) DESC
                LIMIT 2
            ");
            $relatedStmt->execute([$post['id']]);
            $others = $relatedStmt->fetchAll();
        }
    } catch (Exception $e) {
        $post = null;
    }
}

if (!$post) {
    header('Location: ' . bms_url('blog'));
    exit;
}

$config = require __DIR__ . '/includes/config.php';
$page_title = $post['meta_title'] ?: $post['title'];
$page_description = (string) ($post['meta_description'] ?: $post['excerpt'] ?: '');
$page_keywords = !empty($post['meta_keywords']) ? (string) $post['meta_keywords'] : null;
$page_robots = (!empty($post['index_status']) && $post['index_status'] === 'noindex') ? 'noindex, nofollow' : null;
$page_canonical = !empty($post['canonical_url'])
    ? (string) $post['canonical_url']
    : bms_full_url('blog/' . rawurlencode((string) $post['slug']));
$postDate = $post['published_at'] ?? $post['created_at'];
$dateFormatted = date('M j, Y', strtotime((string) $postDate));
$readTime = blog_est_read_time((string) ($post['content'] ?? ''));
$imgRel = ltrim((string) ($post['featured_image'] ?? ''), '/');
$absFile = $imgRel !== '' ? __DIR__ . '/' . $imgRel : '';
$hasFeatured = $imgRel !== '' && is_file($absFile);
$schemaType = !empty($post['schema_type']) ? (string) $post['schema_type'] : 'BlogPosting';
$schemaOrg = !empty($post['schema_organization']) ? (string) $post['schema_organization'] : $config['site_name'];
$schemaLogo = !empty($post['schema_logo'])
    ? (string) $post['schema_logo']
    : bms_full_url('favicon.ico');
$blogSlugBase = rtrim(bms_url('blog'), '/') . '/';
$category = $post['category'] ?: 'Article';

$ld = [
    '@context' => 'https://schema.org',
    '@type' => $schemaType,
    'headline' => $post['title'],
    'description' => $page_description,
    'datePublished' => $postDate,
    'dateModified' => $post['updated_at'] ?? $postDate,
    'publisher' => ['@type' => 'Organization', 'name' => $schemaOrg, 'logo' => ['@type' => 'ImageObject', 'url' => $schemaLogo]],
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $page_canonical],
];
if ($hasFeatured) {
    $ld['image'] = bms_full_url($imgRel);
}

$bms_stylesheets = ['assets/css/blog-detail.css'];
$ubs_extra_classes = 'ubs-blog-detail-page';
$extra_head = '<script type="application/ld+json">' . json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

require __DIR__ . '/includes/portal-open.php';
?>

        <section class="ubs-page-hero">
            <div class="container">
                <p class="small mb-2 opacity-75 text-uppercase fw-semibold" style="letter-spacing:0.08em;">Article</p>
                <ul class="ubs-breadcrumb">
                    <li><a href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>">Home</a></li>
                    <li><a href="<?= htmlspecialchars(bms_url('blog'), ENT_QUOTES, 'UTF-8') ?>">Blog</a></li>
                    <li><span aria-current="page"><?= htmlspecialchars((string) $post['title'], ENT_QUOTES, 'UTF-8') ?></span></li>
                </ul>
            </div>
        </section>

        <article class="ubs-article ubs-article--detail">
            <div class="container-fluid px-3 px-md-4 px-xl-5">
                <div class="ubs-article-card ubs-article-card--wide">
                    <?php if ($hasFeatured): ?>
                        <figure class="bms-featured">
                            <div class="bms-featured__inner">
                                <img src="<?= htmlspecialchars(blog_media_src($imgRel), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) ($post['featured_image_alt'] ?: $post['title']), ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </figure>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="ubs-badge"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="ubs-badge ubs-badge--muted"><?= htmlspecialchars($dateFormatted, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="ubs-badge ubs-badge--muted"><?= htmlspecialchars($readTime, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <h1><?= htmlspecialchars((string) $post['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                    <div class="ubs-prose blog-article-content">
                        <?= blog_apply_image_split_layout(blog_fix_content_images((string) ($post['content'] ?? ''))) ?>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="<?= htmlspecialchars(bms_url('blog'), ENT_QUOTES, 'UTF-8') ?>" class="btn ubs-btn ubs-btn--ghost">All posts</a>
                        <a href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>#contact" class="btn ubs-btn">Contact</a>
                    </div>

                    <?php if ($others !== []): ?>
                        <div class="ubs-related">
                            <h2>Related</h2>
                            <?php foreach ($others as $rel): ?>
                                <?php
                                $rExcerpt = (string) ($rel['excerpt'] ?? '');
                                if (strlen($rExcerpt) > 100) {
                                    $rExcerpt = substr($rExcerpt, 0, 97) . '…';
                                }
                                $rHref = htmlspecialchars($blogSlugBase . rawurlencode((string) $rel['slug']), ENT_QUOTES, 'UTF-8');
                                ?>
                                <a class="ubs-related-card" href="<?= $rHref ?>">
                                    <strong><?= htmlspecialchars((string) $rel['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if ($rExcerpt !== ''): ?><span><?= htmlspecialchars($rExcerpt, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </article>

<?php
require __DIR__ . '/includes/portal-close.php';
