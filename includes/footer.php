<?php
$config = require __DIR__ . '/config.php';
?>
<footer class="bg-dark text-light pt-5 pb-3 mt-5 border-top">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <h5 class="fw-bold mb-2"><?= htmlspecialchars($config['site_name']) ?></h5>
                <p class="mb-1">A modern, professional blog platform for sharing insights, stories, and resources.</p>
                <small class="text-secondary">&copy; <?=date('Y')?> <?= htmlspecialchars($config['site_name']) ?>. All rights reserved.</small>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <h6 class="fw-bold">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= $config['asset_base'] ?>" class="text-light text-decoration-none">Home</a></li>
                    <li><a href="<?= $config['asset_base'] ?>category/technology" class="text-light text-decoration-none">Technology</a></li>
                    <li><a href="<?= $config['asset_base'] ?>category/lifestyle" class="text-light text-decoration-none">Lifestyle</a></li>
                    <li><a href="<?= $config['asset_base'] ?>category/news" class="text-light text-decoration-none">News</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold">Contact</h6>
                <ul class="list-unstyled">
                    <li>Email: <a href="mailto:info@example.com" class="text-light text-decoration-none">info@example.com</a></li>
                    <li>Location: India</li>
                </ul>
            </div>
        </div>
    </div>
</footer>
