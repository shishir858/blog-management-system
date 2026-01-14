<?php
require_once '../includes/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    http_response_code(404);
    echo 'Tag not found.';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM tags WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$tag = $stmt->fetch();

if (!$tag) {
    http_response_code(404);
    echo 'Tag not found.';
    exit;
}

// Fetch posts with this tag
$posts = $pdo->prepare("SELECT p.id, p.title, p.slug, p.created_at, p.meta_description FROM posts p JOIN post_tags pt ON p.id=pt.post_id WHERE pt.tag_id=? AND p.status='published' ORDER BY p.created_at DESC");
$posts->execute([$tag['id']]);
$postsArr = $posts->fetchAll();
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title><?=htmlspecialchars($tag['name'])?> - Blog Tag</title>
    <meta name=\"description\" content=\"Posts tagged <?=htmlspecialchars($tag['name'])?>\">
    <link rel=\"stylesheet\" href=\"../assets/css/style.css\">
</head>
<body>
    <header>
        <h1>Tag: <?=htmlspecialchars($tag['name'])?></h1>
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
