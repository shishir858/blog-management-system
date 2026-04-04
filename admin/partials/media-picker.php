<?php
/** @var string $bms_media_list_url Absolute URL to list-json.php */
if (!isset($bms_media_list_url)) {
    $bms_media_list_url = bms_full_url('admin/media/list-json.php');
}
?>
<div id="bms-media-overlay" class="bms-media-overlay" style="display:none;" aria-hidden="true">
    <div class="bms-media-panel card shadow-lg">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
            <strong>Media library</strong>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="bms-media-close">Close</button>
        </div>
        <div class="card-body" style="max-height:70vh;overflow:auto;">
            <p class="text-muted small mb-2">Click an image to insert.</p>
            <div id="bms-media-grid" class="row g-2"></div>
            <p id="bms-media-empty" class="text-muted small mt-3" style="display:none;">No images in the library yet. Upload from Media or use the editor upload.</p>
        </div>
    </div>
</div>
<style>
.bms-media-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    z-index: 100050;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 24px 12px;
    overflow-y: auto;
}
.bms-media-panel {
    width: 100%;
    max-width: 920px;
    margin-top: 24px;
}
.bms-media-thumb {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
    border-radius: 8px;
    display: block;
}
.bms-media-tile {
    border: 2px solid transparent;
    border-radius: 10px;
    padding: 0;
    background: #f8fafc;
    cursor: pointer;
    overflow: hidden;
}
.bms-media-tile:hover { border-color: var(--wp-accent, #0ea5e9); }
</style>
<script>
(function () {
    var listUrl = <?= json_encode($bms_media_list_url, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
    var overlay = document.getElementById('bms-media-overlay');
    var grid = document.getElementById('bms-media-grid');
    var emptyEl = document.getElementById('bms-media-empty');
    var closeBtn = document.getElementById('bms-media-close');
    window._bmsMediaPickCb = null;

    function close() {
        overlay.style.display = 'none';
        overlay.setAttribute('aria-hidden', 'true');
        window._bmsMediaPickCb = null;
    }

    function open(cb) {
        window._bmsMediaPickCb = cb;
        overlay.style.display = 'flex';
        overlay.setAttribute('aria-hidden', 'false');
        grid.innerHTML = '<p class="text-muted small">Loading…</p>';
        emptyEl.style.display = 'none';
        fetch(listUrl, { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                grid.innerHTML = '';
                var items = (data && data.items) ? data.items : [];
                if (!items.length) {
                    emptyEl.style.display = 'block';
                    return;
                }
                items.forEach(function (it) {
                    var col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3';
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'bms-media-tile w-100';
                    var img = document.createElement('img');
                    img.src = it.url;
                    img.alt = it.file_name || '';
                    img.className = 'bms-media-thumb';
                    img.loading = 'lazy';
                    btn.appendChild(img);
                    btn.addEventListener('click', function () {
                        var cbFn = window._bmsMediaPickCb;
                        close();
                        if (typeof cbFn === 'function') {
                            cbFn(it);
                        }
                    });
                    col.appendChild(btn);
                    grid.appendChild(col);
                });
            })
            .catch(function () {
                grid.innerHTML = '<p class="text-danger small">Could not load media.</p>';
            });
    }

    window.bmsOpenMediaPicker = open;
    window.bmsCloseMediaPicker = close;
    if (closeBtn) closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) close();
    });
})();
</script>
