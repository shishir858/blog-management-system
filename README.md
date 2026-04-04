# Universal Blog Management System

PHP + MySQL blog with an **admin panel** and a **public front** that you can drop into almost any project folder. The front uses a **scoped layout** (`.ubs`) so your host site’s global CSS is less likely to break the blog chrome when you merge routes or iframes.

## Quick start (XAMPP / local)

1. Copy this folder under your web root (e.g. `htdocs/blog-management-system`).
2. Import your SQL dump into MySQL and set credentials in `includes/config.php`.
3. Open `http://localhost/blog-management-system/` for the demo landing page.
4. Open `http://localhost/blog-management-system/admin/login.php` for the admin panel.

## Configuration (`includes/config.php`)

| Key | Purpose |
|-----|---------|
| `site_name` | Brand text in header, footer, and meta. |
| `path_prefix` | URL segment **without** slashes (e.g. `blog-management-system`). Use `''` only if the app lives at the domain root. |
| `public_url` | Optional full origin, e.g. `https://example.com`. If empty, `BASE_URL` is built from the current request. |
| `primary_color` | Accent for buttons, links, and highlights on the public `.ubs` theme. |
| `dsn`, `db_user`, `db_pass` | Database connection. |

On first `require` of `includes/config.php`, constants `BASE_PATH` (e.g. `/blog-management-system`) and `BASE_URL` (full site base) are defined for use in PHP and canonical URLs.

## Embedding in another website

**Recommended:** treat this folder as a **sub-application**:

- Link from your main site menu to `BASE_PATH/index.php`, `BASE_PATH/blog.php`, and pretty URLs `BASE_PATH/blog/{slug}`.
- Load **only** these assets on blog pages: Bootstrap 5 (already linked in partials) and `assets/css/portal.css`. Do not rely on `assets/css/style.css` for new work; it is legacy.

**Scoped UI:** the public shell wraps content in `<div class="ubs">`. Styles in `portal.css` are written under `.ubs` so class names like `.navbar` or `.btn` on the host page do not have to match the blog.

**If the host page must wrap the blog:** add an extra wrapper with a unique ID and paste the blog markup inside; avoid duplicating global `body` rules from the host on the inner blog tree.

## URLs

- Blog listing: `blog.php`
- Post detail: `blog-detail.php?post=slug` or, with Apache `mod_rewrite`, `blog/{slug}` (see root `.htaccess`; set `RewriteBase` to match `path_prefix`).

## Admin panel

- Path: `/admin/` (fixed in `admin/partials/header.php` relative to `BASE_PATH`).
- Styling lives in `admin/partials/admin-style.php` (dashboard chrome). Login page is self-contained in `admin/login.php`.

## File map (public)

| File | Role |
|------|------|
| `index.php` | One-page demo: Home, About, Testimonials, Services, Contact + link to blog. |
| `blog.php` | Post listing. |
| `blog-detail.php` | Single post. |
| `includes/portal-open.php` / `portal-close.php` | Shared header, footer, and assets. |
| `includes/blog-functions.php` | PDO access, read time, content helpers. |

## Requirements

- PHP 8.x with PDO MySQL.
- Apache with `AllowOverride` for `.htaccess` (optional, for pretty `/blog/slug` URLs).

---

For production, set `public_url`, use HTTPS, and restrict `/admin/` (password policy, IP allowlist, or HTTP auth) as needed.
