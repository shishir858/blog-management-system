<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=UTF-8');

$base = rtrim(BASE_URL, '/');
$lastmod = gmdate('c');
$xslHref = htmlspecialchars($base . '/sitemap.xsl', ENT_XML1 | ENT_QUOTES, 'UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<?xml-stylesheet type="text/xsl" href="' . $xslHref . '"?>' . "\n";
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc><?= htmlspecialchars($base . '/page-sitemap.xml', ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($lastmod, ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></lastmod>
  </sitemap>
  <sitemap>
    <loc><?= htmlspecialchars($base . '/post-sitemap.xml', ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($lastmod, ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></lastmod>
  </sitemap>
</sitemapindex>
