<?php
// Admin Posts Management
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login');
    exit;
}
require_once '../includes/config.php';
$pdo = new PDO($config['dsn'], $config['db_user'], $config['db_pass']);
$posts = $pdo->query('SELECT p.*, u.display_name FROM posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: var(--blog-bg); }
        .table thead th { background: var(--blog-header-bg); color: var(--blog-btn-text); }
        .table tbody td { vertical-align: middle; }
        .status-badge { font-size: 0.95rem; border-radius: 6px; padding: 0.3em 0.7em; }
        .status-draft { background: #ffe7c2; color: #b36b00; }
        .status-published { background: #d6f5d6; color: #218838; }
        .status-archived { background: #e2e2e2; color: #555; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="blog-text fw-bold">Posts</h2>
            <a href="add-post" class="btn blog-btn">Add New Post</a>
        </div>
        <div class="card p-3 blog-card-bg shadow-sm border-0">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><a href="edit-post?id=<?php echo $post['id']; ?>" class="blog-link fw-bold"><?php echo htmlspecialchars($post['title']); ?></a></td>
                            <td><?php echo htmlspecialchars($post['display_name'] ?? ''); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="edit-post?id=<?php echo $post['id']; ?>" class="btn btn-sm blog-btn me-2">Edit</a>
                                <a href="delete-post?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
