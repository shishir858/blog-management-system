<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($config) || !is_array($config)) {
    $config = require dirname(__DIR__, 2) . '/includes/config.php';
}
$role = $_SESSION['user_role'] ?? 'admin';
$sbDash = bms_url('admin/dashboard.php');
$sbPosts = bms_url('admin/posts/');
$sbCats = bms_url('admin/categories/');
$sbTags = bms_url('admin/tags/');
$sbUsers = bms_url('admin/users/');
$sbMedia = bms_url('admin/media/');
$sbSettings = bms_url('admin/settings/');
?>
<nav class="col-md-3 col-lg-2 sidebar d-md-block" id="sidebarMenu">
    <div class="sidebar-header">
        <div class="text-center py-3">
            <div class="sidebar-logo">FB</div>
            <div class="sidebar-title">Admin Panel</div>
        </div>
    </div>
    <div class="sidebar-menu">
        <a href="<?= htmlspecialchars($sbDash, ENT_QUOTES, 'UTF-8') ?>">
            <svg width="20" height="20" fill="currentColor" class="me-2"><path d="M3 3h6v8H3V3zm8 0h6v5h-6V3zM3 13h6v5H3v-5zm8-2h6v8h-6v-8z"/></svg>
            Dashboard
        </a>
        <a href="<?= htmlspecialchars($sbPosts, ENT_QUOTES, 'UTF-8') ?>">
            <svg width="20" height="20" fill="currentColor" class="me-2"><path d="M3 3h14a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4a1 1 0 011-1zm1 2v10h12V5H4zm2 2h8v2H6V7zm0 4h8v2H6v-2z"/></svg>
            Posts
        </a>
        <?php if ($role === 'admin' || $role === 'editor'): ?>
        <a href="<?= htmlspecialchars($sbCats, ENT_QUOTES, 'UTF-8') ?>">
            <svg width="20" height="20" fill="currentColor" class="me-2"><path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/></svg>
            Categories
        </a>
        <a href="<?= htmlspecialchars($sbTags, ENT_QUOTES, 'UTF-8') ?>">
            <svg width="20" height="20" fill="currentColor" class="me-2"><path d="M3 3h8l6 6-6 6-6-6V3zm4 5a1 1 0 100-2 1 1 0 000 2z"/></svg>
            Tags
        </a>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
        <a href="<?= htmlspecialchars($sbUsers, ENT_QUOTES, 'UTF-8') ?>">
            <svg width="20" height="20" fill="currentColor" class="me-2"><path d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0H3z"/></svg>
            Users
        </a>
        <?php endif; ?>
        <a href="<?= htmlspecialchars($sbMedia, ENT_QUOTES, 'UTF-8') ?>">
            <svg width="20" height="20" fill="currentColor" class="me-2"><path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 2h12v8l-2.5-2.5a2 2 0 00-2.83 0L8 13.17l-2.5-2.5a2 2 0 00-2.83 0L4 13v-9zm2 4a2 2 0 100-4 2 2 0 000 4z"/></svg>
            Media
        </a>
        <?php if ($role === 'admin'): ?>
        <a href="<?= htmlspecialchars($sbSettings, ENT_QUOTES, 'UTF-8') ?>">
            <svg width="20" height="20" fill="currentColor" class="me-2"><path d="M10 2a2 2 0 00-2 2v1.17a6 6 0 00-1.73 1l-1-.58a2 2 0 00-2.73.73l-1 1.73a2 2 0 00.73 2.73l1 .58a6 6 0 000 2l-1 .58a2 2 0 00-.73 2.73l1 1.73a2 2 0 002.73.73l1-.58a6 6 0 001.73 1V16a2 2 0 002 2h2a2 2 0 002-2v-1.17a6 6 0 001.73-1l1 .58a2 2 0 002.73-.73l1-1.73a2 2 0 00-.73-2.73l-1-.58a6 6 0 000-2l1-.58a2 2 0 00.73-2.73l-1-1.73a2 2 0 00-2.73-.73l-1 .58a6 6 0 00-1.73-1V4a2 2 0 00-2-2h-2zm0 6a4 4 0 110 8 4 4 0 010-8z"/></svg>
            Settings
        </a>
        <?php endif; ?>
    </div>
</nav>
