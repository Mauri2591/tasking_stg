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
    <style>
       table.dataTable td {
  vertical-align: middle !important;
}

.descripcion-cell {
  white-space: normal;
  word-break: break-word;
}

    </style>


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

                            <div style="display: flex; justify-content: end;">
                                <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupVerticalDrop1" type="button"
                                            class="btn btn-primary text-light py-0 px-2 dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Reportes
                                        </button>

                                        <div class="dropdown-menu py-1" aria-labelledby="btnGroupVerticalDrop1">
                                            <a onclick="mdlDeReporteDocx()" class="dropdown-item" href="#"><i title="Descargar Docx" class="fs-22 text-secondary ri-file-word-fill"></i></a>
                                            <a onclick="mdlDeReporteXlsx()" class="dropdown-item" href="#"><i title="Descargar Xlsx" class="fs-22 text-success ri-file-excel-fill"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                                    <th style="width: 20%;text-align: center;">TITULO</th>
                                                    <th style="width: 5%;text-align: center;">REF</th>
                                                    <th style="width: 10%;text-align: center;">PRODUCTO</th>
                                                    <th style="width: 10%;text-align: center;">TAREA</th>
                                                    <th style="width: 10%;text-align: center;">FECHA</th>
                                                    <th style="width: 10%;text-align: center;">INICIO</th>
                                                    <th style="width: 10%;text-align: center;">FIN</th>
                                                    <th style="width: 10%;text-align: center;">CONSUMIDAS</th>
                                                    <th style="width: 40%;text-align: center;">DESCRIPCION</th>
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
                                                    <th style="width: 20%;text-align: center;">TITULO</th>
                                                    <th style="width: 5%;text-align: center;">REF</th>
                                                    <th style="width: 10%;text-align: center;">PRODUCTO</th>
                                                    <th style="width: 10%;text-align: center;">TAREA</th>
                                                    <th style="width: 10%;text-align: center;">FECHA</th>
                                                    <th style="width: 10%;text-align: center;">INICIO</th>
                                                    <th style="width: 10%;text-align: center;">FIN</th>
                                                    <th style="width: 10%;text-align: center;">CONSUMIDAS</th>
                                                    <th style="width: 40%;text-align: center;">DESCRIPCION</th>
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
    include_once __DIR__ . "/Modales/mdlReportes.php";
    ?>

    <script>
        var URL = "<?php echo URL ?>";
        var tablaUsuarios = null;
        var tablaUsuarioLogueado = null;

        document.addEventListener("DOMContentLoaded", function() {

            // =======================
            // SECTOR 4 (tabla con tabs/usuarios)
            // =======================
            if ($('#table_tareas_usuarios').length) {

                tablaUsuarios = $('#table_tareas_usuarios').DataTable({
                    processing: true,
                    serverSide: false,
                    autoWidth: false,
                    responsive: false,
                    scrollX: true,

                    dom: 'Bfrtip',
                    buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],

                    ajax: {
                        url: "../../../../Controller/ctrTimesummary.php?accion=get_tareas_x_usuario",
                        type: "post",
                        dataType: "json",
                        dataSrc: "aaData",
                        data: function(d) {
                            d.usu_id = window.usu_id || 0;
                        }
                    },

                    pageLength: 10,

                    columnDefs: [{
                            targets: 7, // CONSUMIDAS
                            className: 'text-center align-middle',
                            createdCell: function(td) {
                                td.style.textAlign = 'center';
                                td.style.verticalAlign = 'middle';
                            }
                        },
                        {
                            targets: 8, // DESCRIPCIÓN
                            width: '40%',
                            createdCell: function(td) {
                                td.style.textAlign = 'center';
                                td.style.verticalAlign = 'middle';
                                td.style.whiteSpace = 'normal';
                                td.style.wordBreak = 'break-word';
                            }
                        },
                        {
                            targets: '_all',
                            className: 'text-center align-middle'
                        }
                    ]

                });


                // Tabs SOLO en sector 4
                $.post("../../../../Controller/ctrTimesummary.php?accion=get_sectores", function(data) {
                    $("#tab_sectores").html(data);

                    let tabActivo = $("#tab_sectores .nav-link.active");
                    if (tabActivo.length) {
                        cargarUsuarios(tabActivo.data("sector-id"));
                    }
                });

                $("#tab_sectores").on("click", ".nav-link", function(e) {
                    e.preventDefault();
                    $("#tab_sectores .nav-link").removeClass("active");
                    $(this).addClass("active");
                    cargarUsuarios($(this).data("sector-id"));
                });

                function cargarUsuarios(sectorId) {
                    $.post(
                        "../../../../Controller/ctrTimesummary.php?accion=get_usuarios_por_sector", {
                            sector_id: sectorId
                        },
                        function(data) {
                            $("#tab_usuarios_x_sector").html(data);

                            let primerUsuario = $("#tab_usuarios_x_sector span").first();
                            if (primerUsuario.length) {
                                let usu_id = primerUsuario.attr("onclick").match(/\d+/)[0];
                                window.usu_id = usu_id;
                                tablaUsuarios.ajax.reload();
                            }
                        },
                        "html"
                    );
                }
            }

            // =======================
            // RESTO DE SECTORES (tabla simple)
            // =======================
            if ($('#table_get_tareas_x_usuario_x_usu_id').length) {

                tablaUsuarioLogueado = $('#table_get_tareas_x_usuario_x_usu_id').DataTable({
                    processing: true,
                    serverSide: false,
                    autoWidth: false,
                    responsive: false,
                    scrollX: true,

                    dom: 'Bfrtip',
                    buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],

                    ajax: {
                        url: "../../../../Controller/ctrTimesummary.php?accion=get_tareas_x_usuario_x_usu_id",
                        type: "post",
                        dataType: "json",
                        dataSrc: "aaData"
                    },

                    pageLength: 10,

                    columnDefs: [
  {
    targets: 7, // CONSUMIDAS
    className: 'text-center align-middle',
    createdCell: function (td) {
      td.style.textAlign = 'center';
      td.style.verticalAlign = 'middle';
    }
  },
  {
    targets: 8, // DESCRIPCIÓN
    width: '40%',
    createdCell: function (td) {
      td.style.textAlign = 'center';
      td.style.verticalAlign = 'middle';
      td.style.whiteSpace = 'normal';
      td.style.wordBreak = 'break-word';
    }
  },
  {
    targets: '_all',
    className: 'text-center align-middle'
  }
]

                });

            }
        });

        // tu función sigue igual
        function verTareasUsuario(usu_id) {
            window.usu_id = usu_id;
            if (tablaUsuarios) tablaUsuarios.ajax.reload();
        }
    </script>

<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
} ?>