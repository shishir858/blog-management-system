<?php

declare(strict_types=1);

if (!isset($config) || !is_array($config)) {
    $config = require __DIR__ . '/config.php';
}
$page_title = $page_title ?? $config['site_name'];
$page_description = $page_description ?? '';
$primary = $config['primary_color'] ?? '#0ea5e9';
$surface = $config['surface_color'] ?? '#0c4a6e';
$home = bms_url('index.php');
$blog = bms_url('blog');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
    <?php if ($page_description !== ''): ?>
    <meta name="description" content="<?= htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($page_canonical)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($page_canonical, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($page_keywords)): ?>
    <meta name="keywords" content="<?= htmlspecialchars($page_keywords, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (!empty($page_robots)): ?>
    <meta name="robots" content="<?= htmlspecialchars($page_robots, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= htmlspecialchars(bms_url('assets/css/portal.css'), ENT_QUOTES, 'UTF-8') ?>">
    <?php
    foreach ($bms_stylesheets ?? [] as $bms_css) {
        $bms_css = (string) $bms_css;
        if ($bms_css === '') {
            continue;
        }
        ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(bms_url($bms_css), ENT_QUOTES, 'UTF-8') ?>">
        <?php
    }
    ?>
    <?= $extra_head ?? '' ?>
</head>
<body>
<?php
$bms_root = 'ubs';
if (!empty($ubs_extra_classes)) {
    $bms_root .= ' ' . trim((string) $ubs_extra_classes);
}
?>
<div class="<?= htmlspecialchars($bms_root, ENT_QUOTES, 'UTF-8') ?>"
     style="--ubs-primary: <?= htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') ?>; --ubs-surface: <?= htmlspecialchars($surface, ENT_QUOTES, 'UTF-8') ?>;">
    <header class="ubs-header">
        <nav class="navbar navbar-expand-lg ubs-navbar">
            <div class="container">
                <a class="navbar-brand ubs-brand" href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?></a>
                <button class="navbar-toggler ubs-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ubsNav" aria-controls="ubsNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="ubsNav">
                    <ul class="navbar-nav ubs-nav gap-lg-2">
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>#home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>#about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>#services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>#testimonials">Testimonials</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($blog, ENT_QUOTES, 'UTF-8') ?>">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($home, ENT_QUOTES, 'UTF-8') ?>#contact">Contact</a></li>
                    </ul>
                    <a class="btn ubs-btn ubs-btn--sm ms-lg-3" href="<?= htmlspecialchars($blog, ENT_QUOTES, 'UTF-8') ?>">Read blog</a>
                </div>
            </div>
        </nav>
    </header>
    <main class="ubs-main">
