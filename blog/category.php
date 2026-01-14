<?php
require_once '../includes/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    http_response_code(404);
    echo 'Category not found.';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    http_response_code(404);
    echo 'Category not found.';
    exit;
}

// Fetch posts in this category
$posts = $pdo->prepare("SELECT p.id, p.title, p.slug, p.created_at, p.meta_description FROM posts p JOIN post_categories pc ON p.id=pc.post_id WHERE pc.category_id=? AND p.status='published' ORDER BY p.created_at DESC");
$posts->execute([$category['id']]);
$postsArr = $posts->fetchAll();
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title><?=htmlspecialchars($category['name'])?> - Blog Category</title>
    <meta name=\"description\" content=\"<?=htmlspecialchars($category['description'])?>\">
    <link rel=\"stylesheet\" href=\"../assets/css/style.css\">
</head>
<body>
    <header>
        <h1>Category: <?=htmlspecialchars($category['name'])?></h1>
    </header>
    <main>
        <ul class=\"post-list\">
            <?php foreach ($postsArr as $post): ?>
                <li>
                    <a href=\"/blog/<?=htmlspecialchars($post['slug'])?>\">
                        <?=htmlspecialchars($post['title'])?>
                    </a>
                    <span class=\"date\"><?=date('d M Y', strtotime($post['created_at']))?></span>
                    <p><?=htmlspecialchars($post['meta_description'])?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
