<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function bms_require_login(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . bms_full_url('admin/login.php'));
        exit;
    }
}

function bms_user_role(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $r = isset($_SESSION['user_role']) && is_string($_SESSION['user_role'])
        ? strtolower(trim($_SESSION['user_role']))
        : 'admin';

    return $r !== '' ? $r : 'admin';
}

function bms_is_admin(): bool
{
    return bms_user_role() === 'admin';
}

function bms_is_editor(): bool
{
    return bms_user_role() === 'editor';
}

function bms_is_author(): bool
{
    return bms_user_role() === 'author';
}

function bms_can_manage_users(): bool
{
    return bms_is_admin();
}

function bms_can_manage_settings(): bool
{
    return bms_is_admin();
}

function bms_can_manage_taxonomies(): bool
{
    return bms_is_admin() || bms_is_editor();
}

function bms_can_edit_all_posts(): bool
{
    return bms_is_admin() || bms_is_editor();
}

function bms_can_moderate_trash(): bool
{
    return bms_is_admin() || bms_is_editor();
}

function bms_can_delete_any_media(): bool
{
    return bms_is_admin() || bms_is_editor();
}

function bms_require_roles(array $roles): void
{
    bms_require_login();
    if (!in_array(bms_user_role(), $roles, true)) {
        header('Location: ' . bms_full_url('admin/dashboard.php'));
        exit;
    }
}

function bms_can_edit_post(?int $postAuthorId): bool
{
    if (bms_can_edit_all_posts()) {
        return true;
    }
    $uid = isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : 0;

    return $postAuthorId !== null && $uid === (int) $postAuthorId;
}

function bms_current_user_id(): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : 0;
}
