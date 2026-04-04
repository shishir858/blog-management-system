<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=UTF-8');

$base = rtrim(BASE_URL, '/');
$adminDisallow = BASE_PATH === '' ? '/admin/' : rtrim(BASE_PATH, '/') . '/admin/';

echo "User-agent: *\n";
echo "Allow: /\n\n";
echo 'Disallow: ' . $adminDisallow . "\n\n";
echo 'Sitemap: ' . $base . "/sitemap.xml\n";
