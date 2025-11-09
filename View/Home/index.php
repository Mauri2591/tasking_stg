<?php
require_once "../../Config/Conexion.php";
require_once __DIR__ . "/../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../Config/Config.php";
    require_once __DIR__ . "/../../Model/Clases/Headers.php";
?>

    <?php
    include_once __DIR__ . "/Public/Template/head.php";
    ?>
    <style>
        .scroll-fina {
            overflow-x: auto;
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: #bbb transparent;
            /* Firefox */
        }

        /* WebKit (Chrome, Safari, Edge) */
        .scroll-fina::-webkit-scrollbar {
            height: 1px;
            /* grosor de la barra horizontal */
        }

        .scroll-fina::-webkit-scrollbar-track {
            background: transparent;
        }

        .scroll-fina::-webkit-scrollbar-thumb {
            background-color: #bbb;
            border-radius: 5px;
        }
    </style>
    <?php
    include_once __DIR__ . "/Public/Template/main_content.php";
    include_once __DIR__ . "/../Home/Gestion/Usuarios/Modals/mdlEditaPerfil.php";
    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Inicio</h4>
                        <?php if (isset($_SESSION['bienvenido'])): ?>
                            <div id="text_bienvenido" class="alert alert-success text-center justify-content-center"
                                role="alert">
                                <strong><?php echo $_SESSION['bienvenido']; ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>


        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <p class="text-center"> <span class="badge bg-light text-primary mt-1">Proyectos para trabajar</span>
                    </p>
                    <div class="d-flex flex-row flex-nowrap scroll-fina" style="gap: 1rem;"
                        id="cont_get_categorias_x_sector">

                        <!-- Las tarjetas se inyectan aquí por AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['sector_id'] != "4"): ?>
            <!-- container-fluid -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <p class="text-center"> <span class="badge bg-light text-primary mt-1">Total de proyectos cerrados</span>
                            </p> <canvas id="barra_servicios" width="400" height="110"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-lg-12">
                <div class="card">
                    <div class="row">
                        <p class="text-center"> <span class="badge bg-light text-primary mt-1">Total de proyectos cerrados</span>
                        </p>
                        <div class="col-lg-8">
                            <div class="card">
                                <canvas id="barra_servicios" width="400" height="210"></canvas>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <canvas id="pie_servicios" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php endif; ?>

    </div>
    <!-- End Page-content -->
    <?php
    include_once __DIR__ . "/Public/Template/footer.php";
    ?>

    <script>
        setTimeout(() => {
            <?php unset($_SESSION['bienvenido']) ?>
            document.getElementById("text_bienvenido").style.display = "none";
        }, 3000);
    </script>

    <?php
    if (($_SESSION['sector_id']) != "4"):
    ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                $.post("../../Controller/ctrProyectos.php?proy=get_sectores_x_sector_id",
                    function(data, textStatus, jqXHR) {
                        $("#cont_get_categorias_x_sector").html(data);
                    },
                    "html"
                );

                $.post("../../Controller/ctrProyectos.php?proy=grafico_get_total_servicios",
                    function(data, textStatus, jqXHR) {
                        // Suponiendo que 'data' es un array de objetos tipo: [{cat_nom: 'Pentest', total: 5}, ...]
                        const labels = data.map(elem => elem.cat_nom);
                        const values = data.map(elem => elem.total);

                        const ctx = document.getElementById("barra_servicios").getContext("2d");
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total servicios finalizados',
                                    data: values,
                                    borderWidth: 1,
                                    borderColor: 'rgb(0, 225, 255)',
                                    backgroundColor: 'rgb(10, 179, 156)'
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    },
                    "json"
                );
            });
        </script>
    <?php else: ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                $.post("../../Controller/ctrProyectos.php?proy=get_sectores_x_sector_id",
                    function(data, textStatus, jqXHR) {
                        $("#cont_get_categorias_x_sector").html(data);
                    },
                    "html"
                );

                $.post("../../Controller/ctrProyectos.php?proy=grafico_get_total_servicios",
                    function(data, textStatus, jqXHR) {
                        const labels = data.map(elem => elem.cat_nom);
                        const values = data.map(elem => elem.total);

                        const ctx = document.getElementById("barra_servicios").getContext("2d");
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total servicios finalizados',
                                    data: values,
                                    borderWidth: 1,
                                    borderColor: 'rgb(0, 225, 255)',
                                    backgroundColor: 'rgb(10, 179, 156)'
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    },
                    "json"
                );
                $.post("../../Controller/ctrProyectos.php?proy=grafico_get_total_servicios_por_sector",
                    function(data, textStatus, jqXHR) {
                        // Suponiendo que 'data' es un array de objetos tipo: [{cat_nom: 'Pentest', total: 5}, ...]
                        const labels = data.map(elem => elem.sector_nombre);
                        const values = data.map(elem => elem.total);

                        const ctx = document.getElementById("pie_servicios").getContext("2d");
                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total servicios finalizados',
                                    data: values,
                                    borderWidth: 1
                                }]
                            }
                        });
                    },
                    "json"
                );
            });

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
                        text: "Password vacia, debe ingresar una nueva password",
                        showConfirmButton: true,
                        showCancelButton: true
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "../../Controller/ctrUsuarios.php?usuarios=editarPerfil",
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
                                showCancelButton: false,
                                timer: 1200
                            });
                            $("#modalEditPerfil").modal("hide");
                        },
                        error: function(err) {
                            Swal.fire({
                                icon: "warning",
                                title: "Error",
                                text: "Password vacia, debe ingresar una nueva password",
                                showConfirmButton: true,
                                showCancelButton: true
                            });
                        }
                    });
                }
            }
        </script>

    <?php endif; ?>

<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
} ?>