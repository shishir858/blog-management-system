<?php
$base = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($base === '/' || $base === '\\') $base = '';
$base .= $base ? '/' : '';
echo '<h2>Sitemap</h2>';
echo '<p><a href="' . htmlspecialchars($base) . 'sitemap.xml" target="_blank">Open Live Sitemap XML</a></p>';
