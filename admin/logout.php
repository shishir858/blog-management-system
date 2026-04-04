<?php
session_start();
$_SESSION = [];

// Remove blog panel auth session values explicitly
unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['user_role']);

// Invalidate session cookie to force fresh session
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// Keep redirect on same host/origin to avoid host mismatch
header('Location: login.php');
exit;
