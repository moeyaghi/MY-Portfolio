/* ── CV preview page JS ──────────────────────────────────────────────────── */

function hidePageLoader() {
    var pageLoader = document.getElementById('page-loader');
    if (!pageLoader) { return; }
    pageLoader.classList.add('is-hidden');
    document.body.classList.remove('is-loading');
    document.body.classList.add('is-ready');
}

function buildCvPrintTitle() {
    var now = new Date();
    var year    = String(now.getFullYear());
    var month   = String(now.getMonth() + 1).padStart(2, '0');
    var day     = String(now.getDate()).padStart(2, '0');
    var hours   = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');
    var seconds = String(now.getSeconds()).padStart(2, '0');
    return 'Mohammad_Yaghi_CV_' + year + '-' + month + '-' + day + '_' + hours + minutes + seconds;
}

function downloadCV() {
    var frame = document.getElementById('cv-frame');
    if (!frame || !frame.contentWindow) { return; }

    var printTitle         = buildCvPrintTitle();
    var originalTitle      = document.title;
    var originalFrameTitle = '';

    document.title = printTitle;

    try {
        if (frame.contentDocument) {
            originalFrameTitle = frame.contentDocument.title;
            frame.contentDocument.title = printTitle;
        }
    } catch (_) {}

    frame.contentWindow.focus();
    frame.contentWindow.print();

    window.setTimeout(function () {
        document.title = originalTitle;
        try {
            if (frame.contentDocument) {
                frame.contentDocument.title = originalFrameTitle;
            }
        } catch (_) {}
    }, 1000);
}

// ── Responsive iframe scaler ─────────────────────────────────────────────

var frame        = document.getElementById('cv-frame');
var frameScaler  = document.getElementById('cv-frame-scaler');
var frameShell   = document.querySelector('.cv-frame-shell');
var frameViewport = document.querySelector('.cv-frame-viewport');
var pageCountLabel = document.getElementById('cv-page-count');
var zoomLabel = document.getElementById('cv-zoom-level');
var previewLoaderFallback = window.setTimeout(hidePageLoader, 4000);

function updateCvFrameLayout() {
    if (!frame || !frameScaler || !frameViewport || !frame.contentDocument) { return; }

    var doc    = frame.contentDocument;
    var docEl  = doc.documentElement;
    var body   = doc.body;

    var contentWidth  = Math.max(docEl ? docEl.scrollWidth : 0, body ? body.scrollWidth : 0, 794);
    var contentHeight = Math.max(docEl ? docEl.scrollHeight : 0, body ? body.scrollHeight : 0, 400);
    var availableWidth = Math.max(frameViewport.clientWidth, 280);
    var scaleLimit = document.body.dataset.layout === 'optimized' ? 1.28 : 1;
    var scale = Math.min(scaleLimit, availableWidth / contentWidth);
    var pageCount = doc.querySelectorAll('.page').length || 1;

    if (pageCountLabel) { pageCountLabel.textContent = 'PAGE 1 / ' + pageCount; }
    if (zoomLabel) { zoomLabel.textContent = Math.round(scale * 100) + '%'; }

    frame.style.width     = contentWidth + 'px';
    frame.style.height    = contentHeight + 'px';
    frame.style.transform = 'translateX(-50%) scale(' + scale + ')';
    frameScaler.style.width  = Math.max(availableWidth, contentWidth * scale) + 'px';
    frameScaler.style.height = (contentHeight * scale) + 'px';

    if (frameShell) {
        frameShell.classList.toggle('is-scaled', scale < 0.999);
    }
}

document.body.addEventListener('workstation:layoutchange', updateCvFrameLayout);

if (!frame) { hidePageLoader(); }

frame.addEventListener('load', function () {
    try { updateCvFrameLayout(); } catch (_) {}
    window.clearTimeout(previewLoaderFallback);
    hidePageLoader();
});

frame.addEventListener('error', function () {
    window.clearTimeout(previewLoaderFallback);
    hidePageLoader();
});

window.addEventListener('load', function () {
    try {
        if (frame && frame.contentDocument && frame.contentDocument.readyState === 'complete') {
            updateCvFrameLayout();
            hidePageLoader();
        }
    } catch (_) {
        hidePageLoader();
    }
}, { once: true });

window.addEventListener('resize', function () {
    try { updateCvFrameLayout(); } catch (_) {}
});
