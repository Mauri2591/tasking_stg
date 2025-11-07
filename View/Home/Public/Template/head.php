<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>

    <meta charset="utf-8" />
    <title>Tasking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon"
        href="<?php echo URL ?>/View/Home/Public/velzon/assets/images/favicon.ico?sheet=<?php echo rand(); ?>">

    <!-- Layout config Js -->
    <script src="<?php echo URL ?>/View/Home/Public/velzon/assets/js/layout.js?sheet=<?php echo rand(); ?>"></script>
    <!-- Bootstrap Css -->
    <link href="<?php echo URL ?>/View/Home/Public/velzon/assets/css/bootstrap.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />

    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <!-- Icons Css -->
    <link href="<?php echo URL ?>/View/Home/Public/velzon/assets/css/icons.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?php echo URL ?>/View/Home/Public/velzon/assets/css/app.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?php echo URL ?>/View/Home/Public/velzon/assets/css/custom.min.css?sheet=<?php echo rand(); ?>"
        rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="<?php echo URL ?>\View\Home\Public\velzon\assets\css\icons.css?sheet=<?php echo rand(); ?>">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css?sheet=<?php echo rand() ?>"
        rel="stylesheet">

    <?php include_once BASE_PATH . "View/Home/Gestion/Usuarios/Modals/mdlEditaPerfil.php"; ?>

    <script>
        var URL = "<?php echo URL; ?>";

        function editarPerfil() {
            let formData = new FormData();
            formData.append('usu_pass', document.getElementById("usu_pass").value);
            return formData;
        }

        function btnEditPerfil() {
            $("#modalEditPerfil").modal("show");
            document.getElementById("formEditPerfil").reset();
        }

        function btnFormEditPerfil() {
            let data = editarPerfil();
            let usu_pas = data.get('usu_pass');
            if (usu_pas == '') {
                Swal.fire({
                    icon: "warning",
                    title: "Error",
                    text: "Password vacía, debe ingresar una nueva password",
                    showConfirmButton: true,
                    showCancelButton: true
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: URL + "Controller/ctrUsuarios.php?usuarios=editarPerfil",
                    data: {
                        "usu_pass": usu_pas
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "Bien",
                            text: "Password cambiada correctamente",
                            showConfirmButton: false,
                            timer: 1200
                        });
                        $("#modalEditPerfil").modal("hide");
                    },
                    error: function(err) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Ocurrió un problema al cambiar la password",
                            showConfirmButton: true
                        });
                    }
                });
            }
        }
    </script>

    <style>
        #dt-search-0,
        #dt-search-1,
        #dt-search-2,
        #dt-search-3,
        #dt-search-4,
        #dt-search-5,
        #dt-search-6,
        #dt-search-7  {
            height: 20px;
        }

        .navbar-menu {
            width: 245px;
        }

        .badge {
            white-space: normal !important;
            word-break: break-word;
        }
        tbody{
            color: #505050ff;
            font-size: .9em;
            font-weight: 500;
        }
    </style>
</head>