<?php
require_once __DIR__ . "/Config/Conexion.php";
require_once __DIR__ . "/Config/Config.php";
?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">
<head>
    <meta charset="utf-8" />
    <title content="Tasking - Premium Admin Dashboard">Tasking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="stylesheet" href="<?php echo URL; ?>/style.css?sheet=<?php echo rand(); ?>">
    <!-- App favicon -->
    <link rel="shortcut icon"
        href="<?php echo URL; ?>/View/Home/Public/velzon/assets/images/portada_tasking.png?sheet=<?php echo rand(); ?>">
    <!-- Layout config Js -->
    <script src="<?php echo URL; ?>/View/Home/Public/velzon/assets/js/layout.js?sheet=<?php echo rand(); ?>"></script>
    <!-- Bootstrap Css -->
    <link href="<?php echo URL; ?>/View/Home/Public/velzon/assets/css/bootstrap.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?php echo URL; ?>/View/Home/Public/velzon/assets/css/icons.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?php echo URL; ?>/View/Home/Public/velzon/assets/css/app.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?php echo URL; ?>/View/Home/Public/velzon/assets/css/custom.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />

    <style>
        @keyframes pulseGlow {
            0% {
                opacity: 0.4;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.1);
            }
            100% {
                opacity: 0.4;
                transform: scale(1);
            }
        }
        .bg-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent 70%);
            filter: blur(80px);
            animation: pulseGlow 6s infinite ease-in-out;
        }
    </style>
</head>

<body style="margin:0; font-family:'Poppins',sans-serif; background:#020617; overflow:hidden;">

    <div style="
        position:absolute;
        width:600px;
        height:600px;
        background: radial-gradient(circle, rgba(59,130,246,0.15), transparent 70%);
        filter: blur(100px);
        top:-200px;
        left:-200px;
        animation: float 10s ease-in-out infinite;
        "></div>

    <div style="
            position:absolute;
            width:500px;
            height:500px;
            background: radial-gradient(circle, rgba(34,197,94,0.12), transparent 70%);
            filter: blur(100px);
            bottom:-150px;
            right:-150px;
            animation: float 12s ease-in-out infinite;
            "></div>

    <div style="display:flex; height:100vh; position:relative; z-index:2;">

        <!-- PANEL IZQUIERDO-->
        <div style="
        flex:1;
        display:flex;
        flex-direction:column;
        justify-content:center;
        padding:60px;
        color:#e2e8f0;">

            <h1 style="font-size:4.5rem; font-weight:700; margin-bottom:10px;">
                TASKING
            </h1>

            <div style="color:#64748b; margin-bottom:30px;">
                Plataforma integral de gestión, dimensionamiento y seguimiento de proyectos
            </div>

            <div style="display:flex; flex-direction:column; gap:12px; font-size:14px;">
                <div>📁 Gestión de Proyectos y Servicios</div>
                <div>👥 Gestión de Clientes y equipos</div>
                <div>⏱️ Control de horas</div>
                <div>📐 Dimensionamiento de Proyectos</div>
                <div>📊 Seguimiento de avances y estados</div>
                <div>🔗 API REST para integración con sistemas externos</div>
            </div>

            <!-- TERMINAL-->
            <div style="
                        margin-top:40px;
                        background:#020617;
                        border:1px solid #1e293b;
                        border-radius:10px;
                        padding:15px;
                        font-size:12px;
                        color:#22c55e;
                        font-family:monospace;
                        box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
                    " id="terminalBox">
                Inicializando plataforma...
            </div>

        </div>

        <div style="
        width:420px;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:30px;">
            <div style="
            width:100%;
            background: rgba(0,0,0,0.65);
            border:1px solid rgba(59,130,246,0.25);
            border-radius:16px;
            padding:2rem;
            box-shadow:
                0 0 40px rgba(59,130,246,0.15),
                inset 0 0 10px rgba(255,255,255,0.03);
            backdrop-filter: blur(12px);
        ">
                <div style="text-align:center; margin-bottom:20px;">
                    <h2 style="color:#3b82f6;">Acceso seguro</h2>
                    <small style="color:#64748b;">
                        Autenticación requerida
                    </small>
                </div>

                <form method="POST" action="Controller/ctrLogin.php">
                    <input type="hidden" name="token_csrf"
                        <?php $_SESSION['token_csrf'] = bin2hex(random_bytes(32)); ?>
                        value="<?php echo $_SESSION['token_csrf'] ?? '' ?>">

                    <div style="margin-bottom:12px;">
                        <input type="text" name="usu_correo"
                            placeholder="usuario@empresa.com"
                            style="
                            width:100%;
                            padding:12px;
                            border-radius:8px;
                            border:1px solid #1e293b;
                            background:#020617;
                            color:#e2e8f0;
                            outline:none;
                        "
                            onfocus="this.style.border='1px solid #3b82f6'"
                            onblur="this.style.border='1px solid #1e293b'">
                    </div>

                    <div style="margin-bottom:12px;">
                        <input type="password" name="usu_pass"
                            placeholder="Contraseña"
                            style="
                            width:100%;
                            padding:12px;
                            border-radius:8px;
                            border:1px solid #1e293b;
                            background:#020617;
                            color:#e2e8f0;
                            outline:none;
                        "
                            onfocus="this.style.border='1px solid #3b82f6'"
                            onblur="this.style.border='1px solid #1e293b'">
                    </div>

                    <button type="submit" name="btnLogin" value="ingresar"
                        style="
                        width:100%;
                        padding:12px;
                        border:none;
                        border-radius:8px;
                        background:#3b82f6;
                        color:#fff;
                        font-weight:600;
                        cursor:pointer;
                        transition:0.3s;
                    "
                        onmouseover="this.style.background='#2563eb'"
                        onmouseout="this.style.background='#3b82f6'">
                        Ingresar
                    </button>
                   <?php if (isset($_GET['err']) && $_GET['err'] === "err_usu"): ?>
                            <div style="margin-top:10px; color:#f59e0b; text-align:center; font-size:13px;">
                                ⚠️ Complete todos los campos
                            </div>
                     <?php endif; ?>

                    <?php if (isset($_GET['err']) && $_GET['err'] === "err_pass"): ?>
                        <div style="margin-top:10px; color:#ef4444; text-align:center; font-size:13px;">
                            ❌ Credenciales inválidas
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['err']) && $_GET['err'] === "err_csrf"): ?>
                        <div style="margin-top:10px; color:#ef4444; text-align:center; font-size:13px;">
                            🔒 Token CSRF inválido
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

    </div>
    <div style="
        position:fixed;
        bottom:10px;
        width:100%;
        text-align:center;
        font-size:12px;
        color:#475569;">
        © <?php echo date('Y'); ?> Personal-Tech <span style="color:#3b82f6;">(MSSP)</span>
    </div>
</body>

    <script>
        const hayErrorLogin = <?php echo (isset($_GET['err']) ? 'true' : 'false'); ?>;
    </script>

    <script>
        const lines = [
            "[OK] Cargando módulos",
            "[OK] Sincronizando proyectos",
            "[OK] Inicializando timesummary",
            "[OK] Preparando dashboard",
            "[OK] Sistema listo"
        ];

        let i = 0;
        const yaEjecutado = sessionStorage.getItem("terminal_loaded");
        function writeLine() {
            if (i < lines.length) {
                document.getElementById("terminalBox").innerHTML += "<br>" + lines[i];
                i++;
                setTimeout(writeLine, 700);
            } else {
                sessionStorage.setItem("terminal_loaded", "true");
            }
        }
        if (hayErrorLogin) {
            document.getElementById("terminalBox").innerHTML = `
        Inicializando plataforma...<br>
        ${lines.join("<br>")}`;
        } else if (!yaEjecutado) {
            writeLine();
        } else {
            document.getElementById("terminalBox").innerHTML = `
        Inicializando plataforma...<br>
        ${lines.join("<br>")}`;
        }
    </script>
</html>