<?php
require_once __DIR__ . "/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../Model/Clases/Headers.php";

    Headers::get_cors();
?>

    <?php
    include_once __DIR__ . "/../../../../View/Home/Public/Template/head.php";
    include_once __DIR__ . "/../../../../View/Home/Public/Template/main_content.php";
    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4>TAREAS CARGADAS</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->
        <div class="col-lg-12">
            <div class="card-body d-flex bg-light p-0">
                <div class="col-lg-12">

                    <?php if ($_SESSION['sector_id'] == "4"): ?>
                        <div class="card-body">
                            <ul id="tab_sectores" class="nav nav-pills arrow-navtabs nav-success py-0 px-1 mb-3" role="tablist">

                            </ul>
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="tab_nuevos" role="tabpanel">
                                    <div class="card card-body">
                                        <ul id="tab_usuarios_x_sector" class="nav nav-pills arrow-navtabs nav-success py-0 px-1 mb-3" role="tablist">
                                        </ul>

                                        <table style="text-align: center;" id="table_tareas_usuarios">
                                            <thead>
                                                <tr>
                                                    <th style="width: 0%;text-align: center;">CLIENTE</th>
                                                    <th style="width: 5%;text-align: center;">REF</th>
                                                    <th style="width: 5%;text-align: center;">PRODUCTO</th>
                                                    <th style="width: 5%;text-align: center;">TAREA</th>
                                                    <th style="width: 10%;text-align: center;">FECHA</th>
                                                    <th style="width: 5%;text-align: center;">INICIO</th>
                                                    <th style="width: 5%;text-align: center;">FIN</th>
                                                    <th style="width: 5%;text-align: center;">CONSUMIDAS</th>
                                                    <th style="width: 30%;text-align: center;">DESCRIPCION</th>
                                                    <th style="width: 5%;text-align: center;">COLABORADOR</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card-body">
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="tab_nuevos" role="tabpanel">
                                    <div class="card card-body">
                                        <table style="text-align: center;" id="table_get_tareas_x_usuario_x_usu_id">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40%;text-align: center;">CLIENTE</th>
                                                    <th style="width: 5%;text-align: center;">REF</th>
                                                    <th style="width: 5%;text-align: center;">PRODUCTO</th>
                                                    <th style="width: 5%;text-align: center;">TAREA</th>
                                                    <th style="width: 10%;text-align: center;">FECHA</th>
                                                    <th style="width: 5%;text-align: center;">INICIO</th>
                                                    <th style="width: 5%;text-align: center;">FIN</th>
                                                    <th style="width: 5%;text-align: center;">CONSUMIDAS</th>
                                                    <th style="width: 30%;text-align: center;">DESCRIPCION</th>
                                                    <th style="width: 5%;text-align: center;">COLABORADOR</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
    <!-- End Page-content -->
    <?php
    include_once __DIR__ . "/../../../../View/Home/Public/Template/footer.php";
    ?>
    <script>
        var tabla;

        document.addEventListener("DOMContentLoaded", function() {
            // Inicializa DataTable vacía
            tabla = $("#table_tareas_usuarios").DataTable({
                "aProcessing": true,
                "aServerSide": true,
                dom: 'Bfrtip',
                buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
                "ajax": {
                    url: "../../../../Controller/ctrTimesummary.php?accion=get_tareas_x_usuario",
                    type: "post",
                    dataType: "json",
                    data: function(d) {
                        d.usu_id = window.usu_id || 0; // Valor dinámico
                    }
                },
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "language": {
                    "sProcessing": "Procesando...",
                    "sZeroRecords": "No hay tareas registradas.",
                    "sEmptyTable": "Ninguna tarea disponible.",
                    "sInfo": "Mostrando _TOTAL_ registros",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    }
                }
            });


            tabla = $("#table_get_tareas_x_usuario_x_usu_id").DataTable({
                "aProcessing": true,
                "aServerSide": true,
                dom: 'Bfrtip',
                buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
                "ajax": {
                    url: "../../../../Controller/ctrTimesummary.php?accion=get_tareas_x_usuario_x_usu_id",
                    type: "post",
                    dataType: "json",
                    data: function(d) {

                    }
                },
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "language": {
                    "sProcessing": "Procesando...",
                    "sZeroRecords": "No hay tareas registradas.",
                    "sEmptyTable": "Ninguna tarea disponible.",
                    "sInfo": "Mostrando _TOTAL_ registros",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    }
                }
            });

            // Cargar pestañas de sectores
            $.post("../../../../Controller/ctrTimesummary.php?accion=get_sectores", function(data) {
                $("#tab_sectores").html(data);

                // Cargar usuarios del primer sector activo (Ethical Hacking)
                let tabActivo = $("#tab_sectores .nav-link.active");
                let sectorId = tabActivo.data("sector-id");
                cargarUsuarios(sectorId);
            });

            // Click en tab de sector
            $("#tab_sectores").on("click", ".nav-link", function(e) {
                e.preventDefault();
                $("#tab_sectores .nav-link").removeClass("active");
                $(this).addClass("active");
                let sectorId = $(this).data("sector-id");
                cargarUsuarios(sectorId);
            });

            function cargarUsuarios(sectorId) {
                $.post("../../../../Controller/ctrTimesummary.php?accion=get_usuarios_por_sector", {
                        sector_id: sectorId
                    },
                    function(data) {
                        $("#tab_usuarios_x_sector").html(data);

                        // 🔁 Cuando termina de cargar los usuarios, obtener el primero
                        let primerUsuario = $("#tab_usuarios_x_sector span").first();

                        if (primerUsuario.length > 0) {
                            let usu_id = primerUsuario.attr("onclick").match(/\d+/)[0]; // extrae el ID del onclick
                            window.usu_id = usu_id;
                            $("#table_tareas_usuarios").DataTable().ajax.reload(); // recarga la tabla
                        }
                    },
                    "html"
                );
            }

        });

        function verTareasUsuario(usu_id) {
            window.usu_id = usu_id; // Guardamos el id globalmente
            $("#table_tareas_usuarios").DataTable().ajax.reload(); // recarga con el nuevo id
        }
    </script>





<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
} ?>