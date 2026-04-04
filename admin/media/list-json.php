<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/admin-auth.php';
require_once dirname(__DIR__, 2) . '/includes/db.php';

bms_require_login();

header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query("
    SELECT id, file_name, file_path, mime_type
    FROM media
    WHERE mime_type LIKE 'image/%'
    ORDER BY uploaded_at DESC
");
$rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

$items = [];
foreach ($rows as $row) {
    $path = (string) ($row['file_path'] ?? '');
    if ($path === '') {
        continue;
    }
    $items[] = [
        'id' => (int) $row['id'],
        'file_name' => (string) $row['file_name'],
        'url' => bms_full_url($path),
        'path' => $path,
    ];
}

echo json_encode(['items' => $items], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
