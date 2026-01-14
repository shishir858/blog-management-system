<?php
$config = require __DIR__ . '/config.php';
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm w-100">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand fw-bold fs-3" href="<?= $config['asset_base'] ?>"><?= htmlspecialchars($config['site_name']) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= $config['asset_base'] ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $config['asset_base'] ?>category/technology">Technology</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $config['asset_base'] ?>category/lifestyle">Lifestyle</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $config['asset_base'] ?>category/news">News</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
