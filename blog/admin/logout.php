<?php
session_start();
session_unset();
session_destroy();
// Use absolute URL for redirect if on live server
require_once __DIR__ . '/../includes/config.php';
$config = include __DIR__ . '/../includes/config.php';
$base = (isset($config['base_url']) && $config['base_url']) ? $config['base_url'] : './';
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost') {
	// Live server: use absolute URL
	header('Location: ' . rtrim($base, '/') . '/admin/login.php');
} else {
	// Localhost: relative path
	header('Location: login.php');
}
exit;
