<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../includes/db.php';

$success = '';
$error = '';

/* =====================
   Fetch or Create Settings
===================== */
$settings = [];
try {
    $settingsQuery = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $settingsQuery->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    // Create settings table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

// Default values if not set
$defaults = [
    'site_name' => 'Universal Blog',
    'site_tagline' => 'Your awesome blog tagline',
    'site_url' => 'http://localhost',
    'posts_per_page' => '10',
    'date_format' => 'd M Y',
    'time_format' => 'H:i',
    'timezone' => 'UTC',
    'admin_email' => 'admin@example.com',
    'enable_comments' => '1',
    'enable_registration' => '0'
];

foreach ($defaults as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}

/* =====================
   Update Settings
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateSettings = [
        'site_name' => trim($_POST['site_name']),
        'site_tagline' => trim($_POST['site_tagline']),
        'site_url' => trim($_POST['site_url']),
        'posts_per_page' => (int)$_POST['posts_per_page'],
        'date_format' => $_POST['date_format'],
        'time_format' => $_POST['time_format'],
        'timezone' => $_POST['timezone'],
        'admin_email' => trim($_POST['admin_email']),
        'enable_comments' => isset($_POST['enable_comments']) ? '1' : '0',
        'enable_registration' => isset($_POST['enable_registration']) ? '1' : '0'
    ];

    try {
        foreach ($updateSettings as $key => $value) {
            $check = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
            $check->execute([$key]);
            
            if ($check->fetchColumn() > 0) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
                $stmt->execute([$key, $value]);
            }
        }
        $success = 'Settings saved successfully';
        $settings = $updateSettings;
    } catch (PDOException $e) {
        $error = 'Failed to save settings';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - WordPress Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<?php require '../partials/admin-style.php'; ?>
</head>
<body>
<?php require '../partials/header.php'; ?>
<div class="container-fluid">
<div class="row">

<?php require '../partials/sidebar.php'; ?>

<main class="col-md-9 col-lg-10">
    
<div class="wrap">
    <h1 class="wp-heading-inline">General Settings</h1>
    <hr class="wp-header-end">

    <?php if ($success): ?>
    <div class="notice notice-success">
        <p><?= htmlspecialchars($success) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="notice notice-error">
        <p><?= htmlspecialchars($error) ?></p>
    </div>
    <?php endif; ?>

    <form method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="site_name">Site Name</label></th>
                    <td>
                        <input type="text" name="site_name" id="site_name" 
                               value="<?= htmlspecialchars($settings['site_name']) ?>" 
                               class="form-control">
                        <p class="description">The name of your site</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="site_tagline">Tagline</label></th>
                    <td>
                        <input type="text" name="site_tagline" id="site_tagline" 
                               value="<?= htmlspecialchars($settings['site_tagline']) ?>" 
                               class="form-control">
                        <p class="description">In a few words, explain what this site is about</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="site_url">Site URL</label></th>
                    <td>
                        <input type="url" name="site_url" id="site_url" 
                               value="<?= htmlspecialchars($settings['site_url']) ?>" 
                               class="form-control">
                        <p class="description">Your site's URL (e.g., http://example.com)</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="admin_email">Admin Email</label></th>
                    <td>
                        <input type="email" name="admin_email" id="admin_email" 
                               value="<?= htmlspecialchars($settings['admin_email']) ?>" 
                               class="form-control">
                        <p class="description">This address is used for admin purposes</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="posts_per_page">Posts Per Page</label></th>
                    <td>
                        <input type="number" name="posts_per_page" id="posts_per_page" 
                               value="<?= htmlspecialchars($settings['posts_per_page']) ?>" 
                               min="1" max="50" class="form-control" style="max-width:100px;">
                        <p class="description">Number of posts to show per page</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="date_format">Date Format</label></th>
                    <td>
                        <select name="date_format" id="date_format" class="form-control">
                            <option value="d M Y" <?= $settings['date_format'] == 'd M Y' ? 'selected' : '' ?>>
                                <?= date('d M Y') ?> (d M Y)
                            </option>
                            <option value="M d, Y" <?= $settings['date_format'] == 'M d, Y' ? 'selected' : '' ?>>
                                <?= date('M d, Y') ?> (M d, Y)
                            </option>
                            <option value="Y/m/d" <?= $settings['date_format'] == 'Y/m/d' ? 'selected' : '' ?>>
                                <?= date('Y/m/d') ?> (Y/m/d)
                            </option>
                            <option value="d/m/Y" <?= $settings['date_format'] == 'd/m/Y' ? 'selected' : '' ?>>
                                <?= date('d/m/Y') ?> (d/m/Y)
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="time_format">Time Format</label></th>
                    <td>
                        <select name="time_format" id="time_format" class="form-control">
                            <option value="H:i" <?= $settings['time_format'] == 'H:i' ? 'selected' : '' ?>>
                                <?= date('H:i') ?> (24-hour)
                            </option>
                            <option value="g:i A" <?= $settings['time_format'] == 'g:i A' ? 'selected' : '' ?>>
                                <?= date('g:i A') ?> (12-hour)
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="timezone">Timezone</label></th>
                    <td>
                        <select name="timezone" id="timezone" class="form-control">
                            <option value="UTC" <?= $settings['timezone'] == 'UTC' ? 'selected' : '' ?>>UTC</option>
                            <option value="America/New_York" <?= $settings['timezone'] == 'America/New_York' ? 'selected' : '' ?>>New York (EST)</option>
                            <option value="America/Los_Angeles" <?= $settings['timezone'] == 'America/Los_Angeles' ? 'selected' : '' ?>>Los Angeles (PST)</option>
                            <option value="Europe/London" <?= $settings['timezone'] == 'Europe/London' ? 'selected' : '' ?>>London (GMT)</option>
                            <option value="Asia/Kolkata" <?= $settings['timezone'] == 'Asia/Kolkata' ? 'selected' : '' ?>>India (IST)</option>
                            <option value="Asia/Dubai" <?= $settings['timezone'] == 'Asia/Dubai' ? 'selected' : '' ?>>Dubai (GST)</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Discussion</th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="enable_comments" value="1" 
                                       <?= $settings['enable_comments'] == '1' ? 'checked' : '' ?>>
                                Enable comments on posts
                            </label>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Membership</th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="enable_registration" value="1" 
                                       <?= $settings['enable_registration'] == '1' ? 'checked' : '' ?>>
                                Allow anyone to register
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <button type="submit" class="button-primary">Save Changes</button>
        </p>
    </form>
</div>

</main>
</div>
</div>
</body>
</html>
