<?php
// blog/includes/sidebar.php
require_once __DIR__ . '/db.php';
$cats = $pdo->query("SELECT name, slug FROM categories ORDER BY name")->fetchAll();
$tags = $pdo->query("SELECT name, slug FROM tags ORDER BY name LIMIT 15")->fetchAll();

// Contact form mail handler
$contactMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'], $_POST['message'])) {
    $to = 'your@email.com'; // TODO: Replace with your email
    $subject = 'New Contact Form Submission';
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));
    if ($name && $email && $message) {
        $body = "Name: $name\nEmail: $email\nMessage:\n$message";
        $headers = "From: $name <$email>\r\nReply-To: $email";
        if (mail($to, $subject, $body, $headers)) {
            $contactMsg = '<div class="alert alert-success mb-2">Thank you! Your message has been sent.</div>';
        } else {
            $contactMsg = '<div class="alert alert-danger mb-2">Sorry, there was a problem sending your message.</div>';
        }
    } else {
        $contactMsg = '<div class="alert alert-warning mb-2">Please fill all fields correctly.</div>';
    }
}
?>
<aside class="blog-sidebar">
    <!-- Search Card -->
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form class="sidebar-search-form" action="/blog" method="get" autocomplete="off">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search posts..." value="<?=isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''?>">
                    <button class="btn btn-primary sidebar-btn" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Categories Card -->
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="card-title mb-3 fw-bold text-primary">Categories</h5>
            <ul class="list-unstyled mb-0">
                <?php foreach ($cats as $cat): ?>
                    <!-- <li class="mb-2"><a href="category/<?=htmlspecialchars($cat['slug'])?>" class="text-decoration-none text-dark sidebar-link"><i class="bi bi-folder me-1"></i><?=htmlspecialchars($cat['name'])?></a></li> -->
                    <li class="mb-2"><a href="#" class="text-decoration-none text-dark sidebar-link"><i class="bi bi-folder me-1"></i><?=htmlspecialchars($cat['name'])?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <!-- Tags Card -->
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="card-title mb-3 fw-bold text-success">Tags</h5>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($tags as $tag): ?>
                    <!-- <a href="tag/<?=htmlspecialchars($tag['slug'])?>" class="badge bg-light text-dark border sidebar-link">#<?=htmlspecialchars($tag['name'])?></a> -->
                     <a href="#" class="badge bg-light text-dark border sidebar-link">#<?=htmlspecialchars($tag['name'])?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Contact Form Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="card-title mb-3 fw-bold text-info">Contact Us</h5>
            <?= $contactMsg ?>
            <form method="post" action="#" autocomplete="off">
                <div class="mb-2">
                    <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                </div>
                <div class="mb-2">
                    <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                </div>
                <div class="mb-2">
                    <textarea class="form-control" name="message" rows="2" placeholder="Message" required></textarea>
                </div>
                <button type="submit" class="btn btn-info w-100 text-white sidebar-btn">Send</button>
            </form>
        </div>
    </div>
</aside>
