/* ── CV download gate JS ─────────────────────────────────────────────────── */

function onCaptchaVerified() {
    var form = document.querySelector('form.box');
    if (form) { form.submit(); }
}

window.addEventListener('load', function () {
    var pageLoader = document.getElementById('page-loader');
    if (pageLoader) { pageLoader.classList.add('is-hidden'); }
    document.body.classList.remove('is-loading');
    document.body.classList.add('is-ready');

    // Bypass mode: no captcha widget shown, submit immediately.
    if (window.RECAPTCHA_BYPASS) {
        var form = document.querySelector('form.box');
        if (form) { form.submit(); }
    }
}, { once: true });

// Safety fallback: never leave the loader visible indefinitely.
window.setTimeout(function () {
    var pageLoader = document.getElementById('page-loader');
    if (pageLoader) { pageLoader.classList.add('is-hidden'); }
    document.body.classList.remove('is-loading');
    document.body.classList.add('is-ready');
}, 3000);
