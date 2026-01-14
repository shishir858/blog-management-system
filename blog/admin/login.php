<?php
// Admin Login Page - Clean, modern UI
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config = require '../includes/config.php';
    $pdo = new PDO($config['dsn'], $config['db_user'], $config['db_pass']);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password']) && $user['role'] === 'admin') {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['display_name'] ?: $user['username'];
        header('Location: dashboard');
        exit;
    } else {
        $error = 'Invalid credentials or not an admin.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Universal Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #f0f0f1;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
        }
        .login-container { 
            max-width: 380px; 
            width: 100%; 
            padding: 20px;
        }
        .login-card { 
            background: #fff;
            border: 1px solid #c3c4c7;
            border-radius: 4px; 
            box-shadow: 0 1px 3px rgba(0,0,0,.04); 
            padding: 26px 24px 46px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-header h1 {
            font-size: 24px;
            font-weight: 400;
            margin: 0;
            padding: 0;
            color: #1d2327;
        }
        .form-label { 
            font-weight: 500; 
            color: #1d2327; 
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
        }
        .form-control {
            background: #fff;
            color: #2c3338;
            border: 1px solid #8c8f94;
            font-size: 16px;
            padding: 10px;
            border-radius: 4px;
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 100%;
        }
        .form-control:focus {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
            background: #fff;
            color: #2c3338;
            outline: 2px solid transparent;
        }
        .submit-btn {
            background: #2271b1;
            color: #fff;
            font-weight: 500;
            font-size: 14px;
            border-radius: 3px;
            border: 1px solid #2271b1;
            padding: 8px 12px;
            width: 100%;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            margin-top: 10px;
            height: 40px;
        }
        .submit-btn:hover {
            background: #135e96;
            border-color: #135e96;
            color: #fff;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            font-size: 14px;
        }
        .alert-danger {
            color: #b32d2e;
            background: #fcf0f1;
            border-color: #d63638;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #646970;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Universal Blog</h1>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">Log In</button>
            </form>
        </div>
        <p class="login-footer">&copy; <?php echo date('Y'); ?> Universal Blog</p>
    </div>
</body>
</html>
