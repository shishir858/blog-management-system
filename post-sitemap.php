<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/blog-functions.php';

header('Content-Type: application/xml; charset=UTF-8');

$posts = [];

$pdo = blog_get_pdo();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, COALESCE(published_at, created_at) AS dt FROM posts WHERE status='published' ORDER BY COALESCE(published_at, created_at) DESC");
        $posts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (Throwable $e) {
        $posts = [];
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= htmlspecialchars(bms_full_url('blog'), ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
<?php foreach ($posts as $p): ?>
<?php
    if (empty($p['slug'])) {
        continue;
    }
    $slug = (string) $p['slug'];
    if ($slug === '' || !preg_match('/^[a-zA-Z0-9-]+$/', $slug)) {
        continue;
    }
    $loc = bms_full_url('blog/' . $slug);
    $lm = !empty($p['dt']) ? gmdate('c', strtotime((string) $p['dt'])) : gmdate('c');
?>
  <url>
    <loc><?= htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($lm, ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
</urlset>
