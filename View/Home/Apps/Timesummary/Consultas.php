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
        table.dataTable {
            width: 100% !important;
            table-layout: fixed;
        }

        table.dataTable td {
            word-wrap: break-word;
            white-space: normal !important;
        }

        #tab_usuarios_x_sector span.badge {
            cursor: pointer;
            opacity: 0.7;
            transition: all 0.2s ease-in-out;
        }

        #tab_usuarios_x_sector span.badge:hover {
            opacity: 1;
        }

        #tab_usuarios_x_sector span.badge.active {
            background-color: #405189 !important;
            border-color: #0a58ca !important;
            opacity: 1;
        }
    </style>


    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light">
                        <span class="fs-12 badge bg-primary text-light">Tareas Cargadas</span>
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
                                <div class="btn-group-vertical" role="group">
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

                            <!-- TAB SECTORES: solo para sector_id == 4 -->
                            <ul id="tab_sectores" class="nav nav-pills arrow-navtabs nav-success py-0 px-1 mb-3" role="tablist"></ul>

                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="tab_nuevos" role="tabpanel">
                                    <div class="card card-body">
                                        <ul id="tab_usuarios_x_sector" class="nav nav-pills arrow-navtabs nav-success py-0 px-1 mb-3" role="tablist"></ul>
                                        <table style="text-align: center;" id="table_tareas_usuarios">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%;text-align: center;">TITULO</th>
                                                    <th style="width: 10%;text-align: center;">REF</th>
                                                    <th style="width: 20%;text-align: center;">PRODUCTO</th>
                                                    <th style="width: 10%;text-align: center;">TAREA</th>
                                                    <th style="width: 10%;text-align: center;">FECHA</th>
                                                    <th style="width: 10%;text-align: center;">INICIO</th>
                                                    <th style="width: 10%;text-align: center;">FIN</th>
                                                    <th style="width: 20%;text-align: center;">CONSUMIDAS</th>
                                                    <th style="width: 40%;text-align: center;">DESCRIPCION</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($_SESSION['sector_id'] != 4 && $_SESSION['lider'] == "SI"): ?>
                        <div class="card-body">
                            <div style="display: flex; justify-content: end;">
                                <div class="btn-group-vertical" role="group">
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupVerticalDrop1" type="button"
                                            class="btn btn-primary text-light my-2 py-0 px-2 dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Reportes
                                        </button>
                                        <div class="dropdown-menu py-1" aria-labelledby="btnGroupVerticalDrop1">
                                            <a onclick="mdlDeReporteXlsx()" class="dropdown-item" href="#"><i title="Descargar Xlsx" class="fs-22 text-success ri-file-excel-fill"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SIN tab_sectores: el lider ve solo su sector directo -->
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="tab_nuevos" role="tabpanel">
                                    <div class="card card-body">
                                        <ul id="tab_usuarios_x_sector" class="nav nav-pills arrow-navtabs nav-success py-0 px-1 mb-3" role="tablist"></ul>
                                        <table style="text-align: center;" id="table_tareas_usuarios">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%;text-align: center;">TITULO</th>
                                                    <th style="width: 10%;text-align: center;">REF</th>
                                                    <th style="width: 20%;text-align: center;">PRODUCTO</th>
                                                    <th style="width: 10%;text-align: center;">TAREA</th>
                                                    <th style="width: 10%;text-align: center;">FECHA</th>
                                                    <th style="width: 10%;text-align: center;">INICIO</th>
                                                    <th style="width: 10%;text-align: center;">FIN</th>
                                                    <th style="width: 20%;text-align: center;">CONSUMIDAS</th>
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
                                                    <th style="width: 10%;text-align: center;">CLIENTE</th>
                                                    <th style="width: 5%;text-align: center;">REF</th>
                                                    <th style="width: 5%;text-align: center;">PRODUCTO</th>
                                                    <th style="width: 5%;text-align: center;">TAREA</th>
                                                    <th style="width: 10%;text-align: center;">FECHA</th>
                                                    <th style="width: 5%;text-align: center;">INICIO</th>
                                                    <th style="width: 5%;text-align: center;">FIN</th>
                                                    <th style="width: 5%;text-align: center;">CONSUMIDAS</th>
                                                    <th style="width: 30%;text-align: center;">DESCRIPCION</th>
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
        var tabla;
        var URL = "<?php echo URL ?>";
        document.addEventListener("DOMContentLoaded", function() {

            tabla = $("#table_tareas_usuarios").DataTable({
                "aProcessing": true,
                "aServerSide": true,
                order: [
                    
                ],
                autoWidth: false,
                dom: 'Bfrtip',
                columns: [{
                        width: "25%"
                    }, // CLIENTE
                    {
                        width: "10%"
                    }, // REF
                    {
                        width: "20%"
                    }, // PRODUCTO
                    {
                        width: "15%"
                    }, // TAREA
                    {
                        width: "15%"
                    }, // FECHA
                    {
                        width: "15%"
                    }, // INICIO
                    {
                        width: "15%"
                    }, // FIN
                    {
                        width: "25%"
                    }, // CONSUMIDAS
                    {
                        width: "40%"
                    }, // DESCRIPCION
                ],

                buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
                "ajax": {
                    url: "../../../../Controller/ctrTimesummary.php?accion=get_tareas_x_usuario",
                    type: "post",
                    dataType: "json",
                    data: function(d) {
                        d.usu_id = window.usu_id || 0;
                    }
                },

                // 🔴 ESTO ES LO ÚNICO NUEVO
                columnDefs: [{
                    targets: "_all",
                    className: "text-center"
                }],

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
                autoWidth: false,
                order: [
                   
                ], 
                dom: 'Bfrtip',
                columns: [{
                        width: "25%"
                    }, // CLIENTE
                    {
                        width: "10%"
                    }, // REF
                    {
                        width: "20%"
                    }, // PRODUCTO
                    {
                        width: "15%"
                    }, // TAREA
                    {
                        width: "15%"
                    }, // FECHA
                    {
                        width: "15%"
                    }, // INICIO
                    {
                        width: "15%"
                    }, // FIN
                    {
                        width: "20%"
                    }, // CONSUMIDAS
                    {
                        width: "40%"
                    }, // DESCRIPCION
                ],

                buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
                "ajax": {
                    url: "../../../../Controller/ctrTimesummary.php?accion=get_tareas_x_usuario_x_usu_id",
                    type: "post",
                    dataType: "json"
                },

                // 🔴 ESTO ES LO ÚNICO NUEVO
                columnDefs: [{
                    targets: "_all",
                    className: "text-center"
                }],

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
            // $.post("../../../../Controller/ctrTimesummary.php?accion=get_sectores", function(data) {
            //     $("#tab_sectores").html(data);
            //     let tabActivo = $("#tab_sectores .nav-link.active");
            //     let sectorId = tabActivo.data("sector-id");
            //     cargarUsuarios(sectorId);
            // });
            // $("#tab_sectores").on("click", ".nav-link", function(e) {
            //     e.preventDefault();
            //     $("#tab_sectores .nav-link").removeClass("active");
            //     $(this).addClass("active");
            //     let sectorId = $(this).data("sector-id");
            //     cargarUsuarios(sectorId);
            // });

            //VA DESPUÉS DE INICIALIZAR LAS TABLAS
            function cargarUsuarios(sectorId) {
                $.post("../../../../Controller/ctrTimesummary.php?accion=get_usuarios_por_sector", {
                        sector_id: sectorId
                    },
                    function(data) {
                        $("#tab_usuarios_x_sector").html(data);
                        let primerUsuario = $("#tab_usuarios_x_sector span").first();
                        if (primerUsuario.length > 0) {
                            let usu_id = primerUsuario.attr("onclick").match(/\d+/)[0];
                            window.usu_id = usu_id;

                            // Marcar el primero como activo visualmente
                            primerUsuario.addClass("active");

                            $("#table_tareas_usuarios").DataTable().ajax.reload();
                        }
                    },
                    "html");
            }

            <?php if ($_SESSION['sector_id'] == "4"): ?>
                $.post("../../../../Controller/ctrTimesummary.php?accion=get_sectores", function(data) {
                    $("#tab_sectores").html(data);
                    let sectorId = $("#tab_sectores .nav-link.active").data("sector-id");
                    cargarUsuarios(sectorId);
                });

                $("#tab_sectores").on("click", ".nav-link", function(e) {
                    e.preventDefault();
                    $("#tab_sectores .nav-link").removeClass("active");
                    $(this).addClass("active");
                    cargarUsuarios($(this).data("sector-id"));
                });

            <?php elseif ($_SESSION['lider'] == "SI"): ?>
                // Lider: carga directo los usuarios de su sector_id
                cargarUsuarios(<?php echo (int)$_SESSION['sector_id']; ?>);
            <?php endif; ?>

        });

        function validaridCheckValidarCliente(idCheck, idInput) {
            $(idCheck).off("change");
            $(idCheck).on("change", function() {
                const checked = $(this).is(":checked");
                $(this).val(checked ? "SI" : "NO");
                $(idInput).prop("disabled", !checked);
            });
        }

        function mdlDeReporteDocx() {
            $("#hiddenIdClienteDocx").val('');
            $("#getNombreClienteDocx").val('');
            $("#nombreClienteDocx").val('');
            $("#hiddenDocx").val('Docx');
            $("#idCheckValidarClienteDocx").prop("checked", false);
            $("#nombreClienteDocx").prop("disabled", true);
            $("#mdlReportesDocx").modal("show");
            validaridCheckValidarCliente("#idCheckValidarClienteDocx", "#nombreClienteDocx");
            document.getElementById("nombreClienteDocx").addEventListener("input", inputNombreClienteDocx);
        }


        function inputNombreClienteDocx() {
            let texto = $("#nombreClienteDocx").val().trim();
            if (texto === "") {
                $("#hiddenIdClienteDocx").val('');
                $("#getNombreClienteDocx").val('');
                return;
            }
            $.post(
                "../../../../Controller/ctrTimesummary.php?accion=getNombreCliente", {
                    client_rs: texto
                },
                function(data) {
                    if (data && data.cliente_id) {
                        $("#getNombreClienteDocx").val(data.client_rs);
                        $("#hiddenIdClienteDocx").val(data.cliente_id);
                    } else {
                        $("#getNombreClienteDocx").val('');
                        $("#hiddenIdClienteDocx").val('');
                    }
                },
                "json"
            );
        }

        function inputNombreClienteXlsx() {
            let valor = $("#nombreClienteXlsx").val().trim();
            if (valor === "") {
                $("#hiddenIdClienteXlsx").val('');
                $("#getNombreClienteXlsx").val('');
                return;
            }
            $.post(
                "../../../../Controller/ctrTimesummary.php?accion=getNombreCliente", {
                    client_rs: valor
                },
                function(data) {
                    if (data && data.cliente_id) {
                        $("#getNombreClienteXlsx").val(data.client_rs);
                        $("#hiddenIdClienteXlsx").val(data.cliente_id);
                    } else {
                        $("#getNombreClienteXlsx").val('');
                        $("#hiddenIdClienteXlsx").val('');
                    }
                },
                "json"
            );
        }

        function mdlDeReporteXlsx() {
            $("#hiddenIdClienteXlsx").val('');
            $("#getNombreClienteXlsx").val('');
            $("#nombreClienteXlsx").val('');
            $("#idCheckValidarClienteXlsx").prop("checked", false);
            $("#nombreClienteXlsx").prop("disabled", true);
            $("#mdlReportesXlsx").modal("show");
            validaridCheckValidarCliente("#idCheckValidarClienteXlsx", "#nombreClienteXlsx");
            document
                .getElementById("nombreClienteXlsx")
                .addEventListener("input", inputNombreClienteXlsx);
        }

        function verTareasUsuario(usu_id) {
            window.usu_id = usu_id;

            // quitar active a todos los usuarios
            $("#tab_usuarios_x_sector span.badge").removeClass("active");

            // marcar activo el que coincide con el usu_id
            $("#tab_usuarios_x_sector span.badge")
                .filter(function() {
                    return $(this).attr("onclick").includes("(" + usu_id + ")");
                })
                .addClass("active");

            $("#table_tareas_usuarios").DataTable().ajax.reload();
        }
    </script>

<?php } else {
    header("Location:" . URL);
    exit;
} ?>