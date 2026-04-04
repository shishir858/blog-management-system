<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function blog_get_pdo(): ?PDO
{
    global $pdo;

    return isset($pdo) && $pdo instanceof PDO ? $pdo : null;
}

function blog_est_read_time(string $text): string
{
    $words = str_word_count(strip_tags($text));
    $minutes = max(1, (int) ceil($words / 200));

    return $minutes . ' min read';
}

/**
 * If URL/path contains our uploads folder, return current install URL for that file.
 * Fixes: old wrong domain/path, TinyMCE absolute URLs from another folder, pretty /blog/slug breaking relative src.
 */
function blog_normalize_media_url(string $src): string
{
    $raw = trim(html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($raw === '') {
        return '';
    }

    if (preg_match('#^data:#i', $raw)) {
        return $raw;
    }

    if (str_starts_with($raw, '//')) {
        $raw = 'https:' . $raw;
    }

    // Any occurrence of assets/uploads/... (delimiter cannot be # — hash appears in URLs)
    if (preg_match('~(assets/uploads/[^\s"\'<>?]+)~i', $raw, $m)) {
        return bms_full_url($m[1]);
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $origin = ($https ? 'https' : 'http') . '://' . $host;

    if (preg_match('#^https?://#i', $raw)) {
        return $raw;
    }

    if ($raw[0] === '/') {
        $path = parse_url($raw, PHP_URL_PATH) ?? $raw;

        return $origin . $path;
    }

    return rtrim(BASE_URL, '/') . '/' . ltrim($raw, '/');
}

/**
 * Rewrite img src (and srcset) in post HTML so images load on any route and after moves.
 */
function blog_fix_content_images(string $html): string
{
    if ($html === '') {
        return '';
    }

    $html = preg_replace_callback(
        '/<img(\s[^>]*?)\bsrc\s*=\s*([\'"])([^\'"]*)\2/i',
        static function (array $m): string {
            $url = blog_normalize_media_url($m[3]);

            return '<img' . $m[1] . 'src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';
        },
        $html
    ) ?? $html;

    return preg_replace_callback(
        '/<img(\s[^>]*?)\bsrcset\s*=\s*([\'"])([^\'"]*)\2/i',
        static function (array $m): string {
            $fixed = blog_fix_srcset_value($m[3]);

            return '<img' . $m[1] . 'srcset="' . htmlspecialchars($fixed, ENT_QUOTES, 'UTF-8') . '"';
        },
        $html
    ) ?? $html;
}

function blog_fix_srcset_value(string $srcset): string
{
    $srcset = trim(html_entity_decode($srcset, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($srcset === '') {
        return '';
    }
    $parts = preg_split('/\s*,\s*/', $srcset) ?: [];
    $out = [];
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        if (preg_match('/^(\S+)\s+(\d+w|\d+(?:\.\d+)?x)$/i', $part, $mm)) {
            $out[] = blog_normalize_media_url($mm[1]) . ' ' . $mm[2];
        } else {
            $out[] = blog_normalize_media_url($part);
        }
    }

    return implode(', ', $out);
}

/**
 * Public URL for a stored media path (featured image, etc.).
 */
function blog_media_src(string $relativePath): string
{
    $relativePath = ltrim($relativePath, '/');

    return blog_normalize_media_url($relativePath);
}

function blog_dom_is_media_block(DOMElement $el): bool
{
    $t = strtolower($el->tagName);
    if ($t === 'img') {
        return true;
    }
    if ($t === 'figure') {
        return $el->getElementsByTagName('img')->length > 0;
    }
    if ($t === 'p') {
        return blog_dom_p_is_image_only($el);
    }
    if ($t === 'div') {
        return blog_dom_div_is_media_wrapper($el);
    }

    return false;
}

/**
 * TinyMCE often wraps the image in <span> or nests it — use subtree img count + no visible text.
 */
function blog_dom_p_is_image_only(DOMElement $p): bool
{
    if ($p->getElementsByTagName('img')->length !== 1) {
        return false;
    }
    $txt = preg_replace('/\x{00A0}/u', ' ', $p->textContent ?? '');

    return trim($txt) === '';
}

/**
 * e.g. <div style="text-align:center"><img></div> or wrapper around image-only <p>.
 */
function blog_dom_div_is_media_wrapper(DOMElement $div): bool
{
    if ($div->getElementsByTagName('img')->length !== 1) {
        return false;
    }
    $txt = preg_replace('/\x{00A0}/u', ' ', $div->textContent ?? '');

    return trim($txt) === '';
}

/**
 * Right column beside an image: paragraphs, blockquote, lists (ul/ol), or a div that only wraps those (no media).
 */
function blog_dom_is_split_followup(DOMElement $el): bool
{
    $t = strtolower($el->tagName);
    if (in_array($t, ['p', 'blockquote', 'ul', 'ol'], true)) {
        return true;
    }
    if ($t !== 'div') {
        return false;
    }
    if ($el->getElementsByTagName('img')->length > 0) {
        return false;
    }
    if ($el->getElementsByTagName('figure')->length > 0) {
        return false;
    }
    if ($el->getElementsByTagName('table')->length > 0) {
        return false;
    }

    return $el->getElementsByTagName('p')->length > 0
        || $el->getElementsByTagName('blockquote')->length > 0
        || $el->getElementsByTagName('ul')->length > 0
        || $el->getElementsByTagName('ol')->length > 0;
}

/**
 * Wrap each content image + following blocks (p, lists, etc.) in a 50/50 row (see .bms-split-row in blog-detail.css).
 * Run after blog_fix_content_images().
 */
function blog_apply_image_split_layout(string $html): string
{
    if (trim($html) === '') {
        return $html;
    }

    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $wrapped = '<?xml encoding="UTF-8"?><div id="bms-wrap">' . $html . '</div>';
    if (@$dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD) === false) {
        libxml_clear_errors();

        return $html;
    }
    libxml_clear_errors();

    $wrap = $dom->getElementById('bms-wrap');
    if (!$wrap instanceof DOMElement) {
        return $html;
    }

    /* Unwrap TinyMCE’s outer wrapper divs until we hit real siblings (p, img, …). */
    $target = $wrap;
    for ($depth = 0; $depth < 8; $depth++) {
        $elementChildren = [];
        foreach ($target->childNodes as $c) {
            if ($c->nodeType === XML_ELEMENT_NODE && $c instanceof DOMElement) {
                $elementChildren[] = $c;
            }
        }
        if (count($elementChildren) !== 1 || strtolower($elementChildren[0]->tagName) !== 'div') {
            break;
        }
        $target = $elementChildren[0];
    }

    $buffer = iterator_to_array($target->childNodes);
    foreach ($buffer as $n) {
        if ($n->parentNode) {
            $n->parentNode->removeChild($n);
        }
    }

    $i = 0;
    $len = count($buffer);
    /** 1st split: image left · 2nd: image right · 3rd: left again … */
    $splitSeq = 0;
    while ($i < $len) {
        $n = $buffer[$i];
        if ($n->nodeType !== XML_ELEMENT_NODE || !$n instanceof DOMElement) {
            $target->appendChild($n);
            $i++;

            continue;
        }

        if (blog_dom_is_media_block($n)) {
            $row = $dom->createElement('div');
            $media = $dom->createElement('div');
            $media->setAttribute('class', 'bms-split-media');
            $bodyCol = $dom->createElement('div');
            $bodyCol->setAttribute('class', 'bms-split-body');

            $media->appendChild($n);
            $i++;

            while ($i < $len) {
                $next = $buffer[$i];
                if ($next->nodeType === XML_TEXT_NODE) {
                    if (trim($next->textContent ?? '') === '') {
                        $i++;

                        continue;
                    }
                    break;
                }
                if ($next->nodeType !== XML_ELEMENT_NODE || !$next instanceof DOMElement) {
                    break;
                }
                if (!blog_dom_is_split_followup($next)) {
                    break;
                }
                $bodyCol->appendChild($next);
                $i++;
            }

            if (!$bodyCol->hasChildNodes()) {
                $row->setAttribute('class', 'bms-split-row bms-split-row--media-only');
            } else {
                $splitSeq++;
                $rowClass = 'bms-split-row';
                if ($splitSeq % 2 === 0) {
                    $rowClass .= ' bms-split-row--flip';
                }
                $row->setAttribute('class', $rowClass);
            }

            $row->appendChild($media);
            $row->appendChild($bodyCol);
            $target->appendChild($row);

            continue;
        }

        $target->appendChild($n);
        $i++;
    }

    if ($target === $wrap) {
        $out = '';
        foreach ($wrap->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }

    return $dom->saveHTML($target);
}
