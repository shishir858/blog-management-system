<style>
/* WordPress Admin Theme */
:root {
    --wp-blue: #2271b1;
    --wp-blue-dark: #135e96;
    --wp-admin-bg: #f0f0f1;
    --wp-sidebar-bg: #1d2327;
    --wp-sidebar-hover: #2c3338;
    --wp-accent: #2271b1;
    --wp-border: #c3c4c7;
}

body {
    background: var(--wp-admin-bg);
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
    color: #2c3338;
    padding-top: 56px;
}

/* Sidebar - WordPress Style */
.sidebar {
    background: var(--wp-sidebar-bg);
    min-height: calc(100vh - 56px);
    color: #fff;
    padding: 0 !important;
    position: sticky;
    top: 56px;
    left: 0;
    overflow-y: auto;
    z-index: 999;
}

.sidebar-header {
    border-bottom: 1px solid #3c434a;
}

.sidebar-logo {
    width: 50px;
    height: 50px;
    background: var(--wp-accent);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
    color: #fff;
    margin-bottom: 8px;
}

.sidebar-title {
    font-size: 13px;
    color: #f0f0f1;
    opacity: 0.8;
}

.sidebar-menu {
    padding: 8px 0;
}

.sidebar a {
    color: #f0f0f1;
    text-decoration: none;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    border-left: 4px solid transparent;
    transition: all 0.15s ease;
    font-size: 14px;
}

.sidebar a:hover {
    background: var(--wp-sidebar-hover);
    color: #72aee6;
}

.sidebar a.active {
    background: var(--wp-sidebar-hover);
    color: #fff;
    border-left-color: var(--wp-accent);
}

.sidebar a svg {
    opacity: 0.7;
    flex-shrink: 0;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: fixed;
        top: 56px;
        left: -100%;
        width: 250px;
        transition: left 0.3s;
        z-index: 1050;
    }
    .sidebar.show {
        left: 0;
    }
}

.admin-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--wp-accent);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

/* Main Content */
main {
    padding: 20px 30px !important;
}

@media (min-width: 768px) {
    main.col-md-9,
    main.col-lg-10 {
        margin-left: 0;
    }
}

.wp-heading-inline {
    font-size: 23px;
    font-weight: 400;
    margin: 0;
    padding: 9px 0 4px;
    line-height: 1.3;
}

.page-title-action {
    display: inline-block;
    padding: 6px 14px;
    font-size: 13px;
    line-height: 2.15384615;
    border-radius: 3px;
    text-decoration: none;
    background: var(--wp-accent);
    color: #fff;
    border: 1px solid var(--wp-accent);
    margin-left: 10px;
}

.page-title-action:hover {
    background: var(--wp-blue-dark);
    border-color: var(--wp-blue-dark);
    color: #fff;
}

/* Cards */
.page-card,
.wp-card {
    background: #fff;
    border: 1px solid var(--wp-border);
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin-top: 20px;
}

/* Tables - WordPress Style */
.wp-list-table {
    background: #fff;
    border: 1px solid var(--wp-border);
    border-spacing: 0;
    width: 100%;
    margin: 20px 0;
}

.wp-list-table thead th {
    background: #f6f7f7;
    border-bottom: 1px solid var(--wp-border);
    padding: 8px 10px;
    font-weight: 600;
    text-align: left;
    font-size: 14px;
}

.wp-list-table tbody tr {
    border-bottom: 1px solid var(--wp-border);
}

.wp-list-table tbody tr:hover {
    background: #f6f7f7;
}

.wp-list-table tbody td {
    padding: 12px 10px;
    font-size: 14px;
}

/* Buttons - WordPress Style */
.button,
.button-primary,
.button-secondary {
    display: inline-block;
    text-decoration: none;
    font-size: 13px;
    line-height: 2.15384615;
    min-height: 30px;
    margin: 0;
    padding: 0 10px;
    cursor: pointer;
    border: 1px solid #2271b1;
    border-radius: 3px;
    background: #2271b1;
    color: #fff;
    transition: all 0.15s ease;
}

.button-primary:hover {
    background: #135e96;
    border-color: #135e96;
    color: #fff;
}

.button-secondary {
    background: #f6f7f7;
    border-color: #2271b1;
    color: #2271b1;
}

.button-secondary:hover {
    background: #fff;
    border-color: #135e96;
    color: #135e96;
}

.button-danger {
    background: #d63638;
    border-color: #d63638;
    color: #fff;
}

.button-danger:hover {
    background: #b32d2e;
    border-color: #b32d2e;
}

/* Forms - WordPress Style */
.form-table {
    width: 100%;
}

.form-table th {
    padding: 20px 10px 20px 0;
    width: 200px;
    font-weight: 600;
    vertical-align: top;
    text-align: left;
}

.form-table td {
    padding: 15px 10px;
}

.form-table input[type="text"],
.form-table input[type="email"],
.form-table input[type="password"],
.form-table textarea,
.form-table select {
    width: 100%;
    max-width: 400px;
    padding: 8px 10px;
    border: 1px solid #8c8f94;
    border-radius: 4px;
    font-size: 14px;
}

.form-table input[type="text"]:focus,
.form-table textarea:focus {
    border-color: var(--wp-accent);
    box-shadow: 0 0 0 1px var(--wp-accent);
    outline: 2px solid transparent;
}

.description {
    font-size: 13px;
    color: #646970;
    font-style: italic;
    margin-top: 5px;
}

/* Stats Cards */
.stats-card {
    background: #fff;
    border: 1px solid var(--wp-border);
    padding: 20px;
    border-radius: 4px;
    text-align: center;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.stats-card h3 {
    font-size: 14px;
    color: #646970;
    margin: 0 0 10px;
    font-weight: 600;
}

.stats-card .count {
    font-size: 32px;
    font-weight: 400;
    color: var(--wp-accent);
    margin: 0;
}

/* Notices */
.notice {
    background: #fff;
    border-left: 4px solid #72aee6;
    margin: 15px 0;
    padding: 12px;
}

.notice-success {
    border-left-color: #00a32a;
}

.notice-error {
    border-left-color: #d63638;
}

.notice-warning {
    border-left-color: #dba617;
}

/* Action Links */
.row-actions {
    font-size: 13px;
}

.row-actions span {
    display: inline-block;
    padding: 0 4px;
}

.row-actions span:not(:last-child) {
    border-right: 1px solid #c3c4c7;
}

.row-actions a {
    color: #2271b1;
    text-decoration: none;
}

.row-actions a:hover {
    color: #135e96;
}

.row-actions .delete a {
    color: #d63638;
}

/* Responsive */
@media (max-width: 768px) {
    main {
        padding: 15px !important;
    }
    .wp-heading-inline {
        font-size: 20px;
    }
}
</style>