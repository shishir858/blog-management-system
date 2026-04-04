<?php

$config = [
    'site_name' => 'Universal Blog System',
    /** No leading/trailing slashes. Empty string = app at domain root. */
    'path_prefix' => 'blog-management-system',
    /** Optional full origin, e.g. https://example.com — empty = detect from request */
    'public_url' => '',
    'primary_color' => '#0ea5e9',
    'surface_color' => '#0c4a6e',
    'dsn' => 'mysql:host=localhost;dbname=blog-managment-system;charset=utf8mb4',
    'db_user' => 'root',
    'db_pass' => '',
];

if (!defined('BMS_BASE_LOADED')) {
    define('BMS_BASE_LOADED', true);
    $pp = isset($config['path_prefix']) ? trim((string) $config['path_prefix'], '/') : '';
    define('BASE_PATH', $pp === '' ? '' : '/' . $pp);
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $origin = !empty($config['public_url'])
        ? rtrim((string) $config['public_url'], '/')
        : (($https ? 'https' : 'http') . '://' . $host);
    define('BASE_URL', $origin . BASE_PATH);
}

if (!function_exists('bms_url')) {
    /**
     * Absolute path from web root (starts with /).
     */
    function bms_url(string $rel = ''): string
    {
        $rel = ltrim($rel, '/');
        if (BASE_PATH === '') {
            return '/' . $rel;
        }

        return rtrim(BASE_PATH, '/') . '/' . $rel;
    }

    function bms_full_url(string $rel = ''): string
    {
        return rtrim(BASE_URL, '/') . '/' . ltrim($rel, '/');
    }
}

return $config;
