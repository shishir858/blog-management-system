<?php

declare(strict_types=1);

session_start();
$config = require dirname(__DIR__) . '/includes/config.php';
$siteName = $config['site_name'] ?? 'Blog Admin';
$accent = $config['primary_color'] ?? '#0ea5e9';
$surface = $config['surface_color'] ?? '#0c4a6e';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/includes/db.php';
    $login = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Please enter username (or email) and password.';
    } else {
        $stmt = $pdo->prepare('
            SELECT id, username, email, password AS password_hash, display_name, role
            FROM users
            WHERE LOWER(username) = LOWER(?) OR LOWER(email) = LOWER(?)
            LIMIT 1
        ');
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $error = 'Invalid username or password.';
        } else {
            $role = strtolower(trim((string) ($user['role'] ?? '')));
            $allowedRoles = ['admin', 'editor', 'author'];
            if (!in_array($role, $allowedRoles, true)) {
                $error = 'This account does not have access to the admin panel. Ask an administrator to set your role to Author, Editor, or Admin.';
            } else {
                $_SESSION['admin_id'] = (int) $user['id'];
                $_SESSION['admin_name'] = $user['display_name'] ?: $user['username'];
                $_SESSION['user_role'] = $role;
                header('Location: dashboard.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in · <?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --login-accent: <?= htmlspecialchars($accent, ENT_QUOTES, 'UTF-8') ?>;
            --login-surface: <?= htmlspecialchars($surface, ENT_QUOTES, 'UTF-8') ?>;
        }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "DM Sans", system-ui, sans-serif;
            background:
                radial-gradient(ellipse 120% 80% at 10% 20%, color-mix(in srgb, var(--login-accent) 35%, transparent), transparent 50%),
                radial-gradient(ellipse 100% 70% at 90% 80%, color-mix(in srgb, var(--login-accent) 25%, transparent), transparent 45%),
                linear-gradient(145deg, var(--login-surface) 0%, #020617 100%);
            padding: 24px;
        }
        .login-wrap {
            width: 100%;
            max-width: 420px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(2, 6, 23, 0.45);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-brand {
            background: linear-gradient(135deg, var(--login-accent), color-mix(in srgb, var(--login-accent) 55%, #0f172a));
            color: #fff;
            text-align: center;
            padding: 36px 28px 32px;
        }
        .login-brand-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .login-brand h1 {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }
        .login-brand p {
            margin: 8px 0 0;
            opacity: 0.92;
            font-size: 0.9rem;
        }
        .login-body { padding: 32px 28px; }
        .form-label {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }
        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: var(--login-accent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--login-accent) 22%, transparent);
        }
        .input-group-text {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #64748b;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        .password-field { position: relative; }
        .password-field .form-control {
            padding-left: 44px;
            padding-right: 44px;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
        }
        .password-field .left-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            z-index: 2;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #64748b;
            z-index: 2;
            padding: 6px;
            border-radius: 8px;
        }
        .password-toggle:hover { color: var(--login-accent); background: #f1f5f9; }
        .submit-btn {
            background: linear-gradient(135deg, var(--login-accent), color-mix(in srgb, var(--login-accent) 70%, #0369a1));
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-radius: 12px;
            padding: 12px;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px color-mix(in srgb, var(--login-accent) 40%, transparent);
            color: #fff;
        }
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.9rem;
        }
        .login-footer {
            text-align: center;
            padding: 16px 20px;
            background: #f8fafc;
            color: #64748b;
            font-size: 0.85rem;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-brand">
                <div class="login-brand-icon" aria-hidden="true"><i class="fa-solid fa-pen-to-square"></i></div>
                <h1><?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></h1>
                <p>Sign in to manage posts and settings</p>
            </div>
            <div class="login-body">
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username or email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-field">
                            <span class="left-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                                <i class="fa-solid fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt me-2"></i>Log in
                    </button>
                </form>
            </div>
            <div class="login-footer">
                &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            var pwd = document.getElementById('password');
            var icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.setAttribute('aria-label', 'Hide password');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.setAttribute('aria-label', 'Show password');
            }
        });
    </script>
</body>
</html>
