<?php

declare(strict_types=1);

if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/config.php';
}
$year = (int) date('Y');
?>
    </main>
    <footer class="ubs-footer">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-md-5">
                    <div class="ubs-footer-brand"><?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <p class="ubs-footer-text mb-0">Blog panel you can ship beside any front-end. Scoped styles keep the shell predictable when the rest of the site changes.</p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="ubs-footer-heading">Explore</div>
                    <ul class="ubs-footer-links">
                        <li><a href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>#about">About</a></li>
                        <li><a href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>#services">Services</a></li>
                        <li><a href="<?= htmlspecialchars(bms_url('blog'), ENT_QUOTES, 'UTF-8') ?>">Blog</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ubs-footer-heading">Contact</div>
                    <ul class="ubs-footer-links">
                        <li><a href="<?= htmlspecialchars(bms_url('index.php'), ENT_QUOTES, 'UTF-8') ?>#contact">Message form</a></li>
                        <li><a href="mailto:hello@example.com">hello@example.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="ubs-footer-bar">
                <span>&copy; <?= $year ?> <?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="ubs-footer-meta">Universal Blog shell · <code class="ubs-code">.ubs</code> scoped CSS</span>
            </div>
        </div>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
