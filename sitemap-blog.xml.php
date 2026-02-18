<?php
// Prevent accidental output before XML declaration
ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/blog/includes/db.php';

// Get base URL from blog config
$config = require __DIR__ . '/blog/includes/config.php';
$baseUrl = 'https://www.ledtvrepairservicecenter.com' . rtrim($config['base_url'], '/') . '/';

try {
    $stmt = $pdo->query("SELECT slug, updated_at FROM posts WHERE status = 'published' ORDER BY updated_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $dbError = '';
} catch (Exception $e) {
    $posts = [];
    $dbError = $e->getMessage();
}

// Clear output buffer before XML output
if (ob_get_length()) {
    ob_clean();
}

// XML Output
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

if (!empty($dbError)) {
    echo '<!-- Database error: ' . htmlspecialchars($dbError) . ' -->' . "\n";
}

foreach ($posts as $row) {
    $loc = htmlspecialchars($baseUrl . $row['slug'], ENT_QUOTES, 'UTF-8');
    $lastmod = date('c', strtotime($row['updated_at']));

    echo "  <url>\n";
    echo "    <loc>{$loc}</loc>\n";
    echo "    <lastmod>{$lastmod}</lastmod>\n";
    echo "    <priority>0.64</priority>\n";
    echo "  </url>\n";   // ✅ FIX: Properly close <url> tag
}

echo "</urlset>";

ob_end_flush();
?>