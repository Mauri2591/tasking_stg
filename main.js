document.addEventListener("DOMContentLoaded", function () {
    const themeSettings = document.getElementById('theme-settings-offcanvas');
    if (themeSettings) {
        themeSettings.classList.remove('show'); // Oculta visualmente
        themeSettings.style.visibility = 'hidden'; // Previene que se vea
        themeSettings.removeAttribute('aria-modal');
        themeSettings.setAttribute('aria-hidden', 'true');
        themeSettings.style.display = ''; // Devolvelo al default
    }

    document.body.style.overflow = ''; // Devuelve scroll al body
    document.body.style.paddingRight = ''; // Remueve padding agregado por el offcanvas

    function validar_datos_err_usu() {
        setTimeout(() => {
            document.getElementById("err_usu").style.display = "none";
        }, 1300);
    }
    function validar_datos_err_pass() {
        setTimeout(() => {
            document.getElementById("err_pass").style.display = "none";
        }, 1300);
    }
    let url = new URLSearchParams(location.search);
    let param = url.get("err");
    switch (param) {
        case 'err_usu':
            validar_datos_err_usu()
            break;
        case 'err_pass':
            validar_datos_err_pass()
            break;
        default:
            break;
    }
})