<?php
if (!isset($config) || !is_array($config)) {
    $config = require dirname(__DIR__, 2) . '/includes/config.php';
}
$basePath = BASE_PATH === '' ? '/' : BASE_PATH;
$admin_base = rtrim($basePath, '/') . '/admin';
$logout_url = $admin_base . '/logout.php';
$profile_url = $admin_base . '/users/profile.php';
$dashboard_url = $admin_base . '/dashboard.php';
$site_home_url = rtrim($basePath, '/') . '/' ?: '/';
$admin_site_label = $config['site_name'] ?? 'Blog admin';
?>
<!-- WordPress Admin Header -->
<div class="wp-admin-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3 d-md-none" id="sidebarToggle">
                    <span>&#9776;</span>
                </button>
                <a href="<?php echo htmlspecialchars($dashboard_url); ?>" class="header-logo">
                    <strong><?php echo htmlspecialchars($admin_site_label); ?></strong>
                </a>
            </div>
            
            <div class="d-flex align-items-center">
                <a href="<?php echo htmlspecialchars($site_home_url); ?>" target="_blank" class="header-link me-3" title="Visit Site">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor" class="me-1">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14.5a6.5 6.5 0 110-13 6.5 6.5 0 010 13z"/>
                    </svg>
                    <span class="d-none d-lg-inline">Visit Site</span>
                </a>
                
                <div class="dropdown">
                    <a href="#" class="user-menu-toggle d-flex align-items-center text-decoration-none" id="userMenuToggle" onclick="toggleUserMenu(event)">
                        <div class="user-avatar me-2">
                            <?php 
                            $admin_name = $_SESSION['admin_name'] ?? 'Admin';
                            echo strtoupper(substr($admin_name, 0, 1)); 
                            ?>
                        </div>
                        <span class="user-name d-none d-sm-inline"><?= htmlspecialchars($admin_name) ?></span>
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" class="ms-2">
                            <path d="M5 8l5 5 5-5z"/>
                        </svg>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown">
                        <li><a class="dropdown-item" href="<?php echo htmlspecialchars($profile_url); ?>">
                            <svg width="16" height="16" fill="currentColor" class="me-2" style="vertical-align: middle;">
                                <path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2 2H6a5 5 0 00-5 5v1h12v-1a5 5 0 00-5-5z"/>
                            </svg>
                            Edit Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo htmlspecialchars($logout_url); ?>">
                            <svg width="16" height="16" fill="currentColor" class="me-2" style="vertical-align: middle;">
                                <path d="M3 3h8v2H3v10h8v2H3a2 2 0 01-2-2V5a2 2 0 012-2zm10 4l4 4-4 4v-3H7V8h6V5z"/>
                            </svg>
                            Log Out
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.wp-admin-header {
    background: var(--wp-sidebar-bg) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    padding: 0;
    height: 56px;
    display: flex;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1001;
    color: #fff;
}

.wp-admin-header .container-fluid {
    width: 100%;
    padding: 0 20px;
}

.header-logo {
    color: #fff;
    text-decoration: none;
    font-size: 18px;
    letter-spacing: 0.5px;
    transition: color 0.15s;
}

.header-logo:hover {
    color: #72aee6;
}

.header-link {
    color: #f0f0f1;
    text-decoration: none;
    padding: 8px;
    border-radius: 4px;
    transition: all 0.15s;
    display: flex;
    align-items: center;
}

.header-link:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

.sidebar-toggle {
    background: none;
    border: none;
    color: #f0f0f1;
    font-size: 24px;
    cursor: pointer;
    padding: 5px 10px;
    line-height: 1;
}

.sidebar-toggle:hover {
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
}

.user-menu-toggle {
    color: #f0f0f1;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 4px;
    transition: all 0.15s;
    cursor: pointer;
}

.user-menu-toggle:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--wp-accent);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.user-name {
    font-size: 14px;
    font-weight: 500;
    color: #f0f0f1;
}

.user-dropdown {
    min-width: 200px;
    border: 1px solid #c3c4c7;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
    margin-top: 8px !important;
    background: #fff;
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    border-radius: 4px;
    padding: 4px 0;
    z-index: 1050;
}

.user-dropdown.show {
    display: block;
}

.dropdown {
    position: relative;
}

.user-dropdown .dropdown-item {
    padding: 10px 16px;
    font-size: 14px;
    display: flex;
    align-items: center;
    color: #2c3338;
    text-decoration: none;
    transition: background 0.15s;
}

.user-dropdown .dropdown-item:hover {
    background: #f6f7f7;
    color: #2c3338;
}

.user-dropdown .dropdown-item.text-danger {
    color: #d63638 !important;
}

.user-dropdown .dropdown-item.text-danger:hover {
    background: #fcf0f1;
    color: #d63638 !important;
}

@media (max-width: 768px) {
    .wp-admin-header .container-fluid {
        padding: 0 15px;
    }
    .user-name {
        display: none !important;
    }
}
</style>

<script>
// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarMenu');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.user-dropdown');
        const toggle = document.getElementById('userMenuToggle');
        if (dropdown && toggle) {
            if (!dropdown.contains(e.target) && !toggle.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        }
    });
});

// Toggle user menu dropdown
function toggleUserMenu(event) {
    event.preventDefault();
    event.stopPropagation();
    const dropdown = document.querySelector('.user-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}
</script>
