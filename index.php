<?php
require_once __DIR__ . "/Config/Config.php";
// require_once __DIR__ . "/Config/Conexion.php";
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

</head>

<body class="bg-primary">

    <div class="auth-page-wrapper pt-5">
        <!-- auth page content -->
        <div class="auth-page-content p-0">
            <div class="container">
                <!-- <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <h1 style="font-size: 50px; color:white">Tasking</h1>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- end row -->
                <div class="row justify-content-center" style="margin-top: 13.5rem;">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card" style="margin-top: 5rem;">

                            <div class="card-body bg-light"
                                style="border: 1px solid #3f0d66; box-shadow:0 0 20px 5px  #3f0d66;">
                                <div class="text-center">
                                    <div>
                                        <marquee class="text-primary" behavior="" direction=""><span
                                                style="font-family: monospace; color:#3f0d66; font-weight: bold;"
                                                class="fs-22">Bienvenido a
                                                TASKING <i
                                                    class="  ri-git-repository-line text-success fw-normal fs-20 p-0 m-0"></i></span>
                                        </marquee>
                                    </div>

                                </div>
                                <div>
                                    <form method="POST" action="Controller/ctrLogin.php">

                                        <div class="mb-3 mt-2">
                                            <label for="username" class="form-label fw-bold mb-0"
                                                style="color:#3f0d66">Correo</label>
                                            <input type="text" class="form-control" name="usu_correo"
                                                placeholder="Ingrese su email">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold mb-0" style="color:#3f0d66"
                                                for="password-input">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5"
                                                    placeholder="Ingrese su password" name="usu_pass">
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" name="btnLogin" value="ingresar"
                                                type="submit">Ingresar</button>
                                        </div>
                                        <?php if (isset($_GET['err']) && $_GET['err'] === "err_usu"): ?>
                                            <div id="err_usu" class="alert text-center mt-2 alert-warning alert-borderless"
                                                role="alert">
                                                <strong> Error </strong><br> Datos vacios!
                                            </div>
                                        <?php endif; ?>
                                        <?php if (isset($_GET['err']) && $_GET['err'] === "err_pass"): ?>
                                            <div id="err_pass" class="alert text-center mt-2 alert-warning alert-borderless"
                                                role="alert">
                                                <strong> Error </strong><br> Datos no validos!
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->
        <!-- footer -->
        <footer class="footer pt-3 pb-0">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-light fs-16">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> Telecom - MSSP
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/libs/bootstrap/js/bootstrap.bundle.min.js?sheet=<?php echo rand(); ?>">
    </script>
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/libs/simplebar/simplebar.min.js?sheet=<?php echo rand(); ?>">
    </script>
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/libs/node-waves/waves.min.js?sheet=<?php echo rand(); ?>">
    </script>
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/libs/feather-icons/feather.min.js?sheet=<?php echo rand(); ?>">
    </script>
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/js/pages/plugins/lord-icon-2.1.0.js?sheet=<?php echo rand(); ?>">
    </script>
    <script src="<?php echo URL; ?>/View/Home/Public/velzon/assets/js/plugins.js?sheet=<?php echo rand(); ?>"></script>

    <!-- particles js -->
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/libs/particles.js/particles.js?sheet=<?php echo rand(); ?>">
    </script>
    <!-- particles app js -->
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/js/pages/particles.app.js?sheet=<?php echo rand(); ?>">
    </script>
    <!-- password-addon init -->
    <script
        src="<?php echo URL; ?>/View/Home/Public/velzon/assets/js/pages/password-addon.init.js?sheet=<?php echo rand(); ?>">
    </script>
    <script src="main.js?sheet=<?php echo rand(); ?>"></script>
</body>

</html>
