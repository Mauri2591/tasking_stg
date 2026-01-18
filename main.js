document.addEventListener("DOMContentLoaded", function () {
    const themeSettings = document.getElementById('theme-settings-offcanvas');
    if (themeSettings) {
        themeSettings.classList.remove('show');
        themeSettings.style.visibility = 'hidden';
        themeSettings.removeAttribute('aria-modal');
        themeSettings.setAttribute('aria-hidden', 'true');
        themeSettings.style.display = '';
    }
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';

    const url = new URL(window.location.href);
    const param = url.searchParams.get('err');

    function ocultarError(id, paramName) {
        setTimeout(() => {
            const el = document.getElementById(id);
            if (el) el.style.display = "none";
            url.searchParams.delete(paramName);
            history.replaceState(null, '', url.toString());
        }, 1500);
    }

    switch (param) {
        case 'err_usu':
            ocultarError('err_usu', 'err');
            break;

        case 'err_pass':
            ocultarError('err_pass', 'err');
            break;

        case 'csrf':
            ocultarError('token_csrf', 'err');
            break;
    }
});