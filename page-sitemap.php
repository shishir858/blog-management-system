<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=UTF-8');

$lastmod = gmdate('c');

$urls = [
    ['loc' => bms_full_url('index.php'), 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => bms_full_url('blog'), 'priority' => '0.9', 'changefreq' => 'weekly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= htmlspecialchars($u['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($lastmod, ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></lastmod>
    <changefreq><?= htmlspecialchars($u['changefreq'], ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></changefreq>
    <priority><?= htmlspecialchars($u['priority'], ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
