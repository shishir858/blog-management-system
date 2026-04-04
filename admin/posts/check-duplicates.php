<?php
// Quick script to check duplicate slugs in database
require_once '../../includes/db.php';

echo "<h2>Checking for duplicate slugs...</h2>";

// Find all posts with the problematic slug
$slug = 'lcd-tv-repair-services-fix-all-led-tv-brands';
$stmt = $pdo->prepare("SELECT id, title, slug, status FROM posts WHERE slug = ?");
$stmt->execute([$slug]);
$posts = $stmt->fetchAll();

echo "<p><strong>Posts with slug '$slug':</strong></p>";
if (count($posts) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Title</th><th>Slug</th><th>Status</th></tr>";
    foreach ($posts as $post) {
        echo "<tr>";
        echo "<td>{$post['id']}</td>";
        echo "<td>{$post['title']}</td>";
        echo "<td>{$post['slug']}</td>";
        echo "<td>{$post['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (count($posts) > 1) {
        echo "<p style='color:red;'><strong>DUPLICATE FOUND! " . count($posts) . " posts have the same slug!</strong></p>";
    }
} else {
    echo "<p>No posts found with this slug.</p>";
}

// Check all duplicate slugs in the database
echo "<hr><h2>All Duplicate Slugs in Database:</h2>";
$duplicates = $pdo->query("
    SELECT slug, COUNT(*) as count, GROUP_CONCAT(id) as post_ids 
    FROM posts 
    GROUP BY slug 
    HAVING count > 1
")->fetchAll();

if (count($duplicates) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Slug</th><th>Count</th><th>Post IDs</th></tr>";
    foreach ($duplicates as $dup) {
        echo "<tr>";
        echo "<td>{$dup['slug']}</td>";
        echo "<td>{$dup['count']}</td>";
        echo "<td>{$dup['post_ids']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:green;'>No duplicate slugs found in database!</p>";
}
?>
