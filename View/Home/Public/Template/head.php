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
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" /> -->

    <!-- DataTables 1.13 compatible con Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css">

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

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("idCheckValidarUsuPass").addEventListener("change", function() {
                if (this.value == "NO") {
                    this.value = "SI";
                    $("#usu_pass").prop("disabled", false);
                } else if (this.value == "SI") {
                    this.value = "NO"
                    $("#usu_pass").prop("disabled", true);
                }
            });
        })

        function editarPerfil() {
            let formData = new FormData();
            formData.append('usu_nom', document.getElementById("usu_nom").value);
            formData.append('usu_ape', document.getElementById("usu_ape").value);
            formData.append('usu_correo', document.getElementById("usu_correo").value);
            formData.append('usu_pass', document.getElementById("usu_pass").value);
            formData.append('idCheckValidarUsuPass', document.getElementById("idCheckValidarUsuPass").value);
            return formData;
        }
        function btnEditPerfil() {
            $.post(URL + "Controller/ctrUsuarios.php?usuarios=get_usuario_x_id",
                function(data, textStatus, jqXHR) {
                    $("#usu_nom").val(data.usu_nom);
                    $("#usu_ape").val(data.usu_ape);
                    $("#usu_correo").val(data.usu_correo);
                },
                "json"
            );
            
            $("#idCheckValidarUsuPass").val("NO")
            document.getElementById("usu_pass").setAttribute("disabled", "");
            $("#modalEditPerfil").modal("show");
            document.getElementById("formEditPerfil").reset();

        }

        function btnFormEditPerfil() {
            let formData = new FormData();
            formData.append('usu_nom', $("#usu_nom").val());
            formData.append('usu_ape', $("#usu_ape").val());
            formData.append('usu_correo', $("#usu_correo").val());

            const cambiarPass = $("#idCheckValidarUsuPass").prop("checked");
            formData.append('idCheckValidarUsuPass', cambiarPass ? "SI" : "NO");

            if (cambiarPass) {
                formData.append('password', $("#usu_pass").val());
            }

            $.ajax({
                type: "POST",
                url: URL + "Controller/ctrUsuarios.php?usuarios=editarPerfil",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Bien",
                        text: response.Success,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $("#modalEditPerfil").modal("hide");
                },
                error: function(err) {
                    let errorMsg = err.responseJSON?.Error || "Error inesperado.";
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: errorMsg,
                        showConfirmButton: true
                    });
                }
            });
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
        #dt-search-7 {
            height: 20px;
        }

        .navbar-menu {
            width: 245px;
        }

        .badge {
            white-space: normal !important;
            word-break: break-word;
        }

        tbody {
            color: #505050ff;
            font-size: .9em;
            font-weight: 500;
        }

        .dropdown-item{
            padding: 0 1rem;
        }
    </style>
</head>