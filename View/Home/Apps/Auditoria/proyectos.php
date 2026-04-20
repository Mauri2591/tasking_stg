<?php
require_once __DIR__ . "/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../Model/Clases/Headers.php";
    Headers::get_cors();
    include_once __DIR__ . "/../../Public/Template/head.php";
    include_once __DIR__ . "/../../Public/Template/main_content.php";
?>
    <style>
        #tablaAuditoriaProyectos {
            table-layout: fixed;
            width: 100% !important;
        }

        #tablaAuditoriaProyectos td {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light bg-light">
                        <span class="fs-12 badge bg-primary text-light">Logs Proyectos</span>

                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>


        <div class="col-xl-12">
            <div class="card crm-widget">
                <div style="display: flex; justify-content: end; height: 1.5rem; margin:.5rem">
                    <span class="mb-4"><span class="badge bg-primary fs-12">Reporte Logs Proyectos</span><i onclick="mdlRreportePdfProyectos()" class="ri-file-pdf-fill text-danger fs-22" type="button" title="Descargar documento"></i></span>
                </div>
                <div class="card-body p-0">
                    <table id="tablaAuditoriaProyectos" class="table table-hover table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Titulo</th>
                                <th>Referencia</th>
                                <th>Usuario</th>
                                <th>Sector</th>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Estado Usuario</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php include_once __DIR__."/Modals/mdlProyectos.php"; ?>

    <?php
    include_once __DIR__ . "/../../Public/Template/footer.php";
    ?>
<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
}
?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
<script>
    var tabla;
    var URL = "<?php echo URL ?>";
    document.addEventListener("DOMContentLoaded", function() {
        tabla = $("#tablaAuditoriaProyectos").dataTable({
            "ajax": {
                url: URL + "Controller/ctrAuditoria.php?case=get_auditoria_proyectos",
                type: "post",
                dataType: "json",
                error: function(e) {}
            },
            "order": [
                [5, "desc"]
            ],
            "bDestroy": true,
            "responsive": false,
            "bInfo": true,
            "iDisplayLength": 10,
            "autoWidth": false,
            "columnDefs": [{
                    "className": "text-center",
                    "targets": "_all"
                },
                {
                    "targets": 0,
                    "width": "25%"
                },
                {
                    "targets": 1,
                    "width": "10%"
                },
                {
                    "targets": 2,
                    "width": "15%"
                },
                {
                    "targets": 3,
                    "width": "10%"
                },
                {
                    "targets": 4,
                    "width": "13%"
                },
                {
                    "targets": 5,
                    "width": "8%"
                },
                {
                    "targets": 6,
                    "width": "8%"
                }
            ],
            "language": {
                "sProcessing": "Procesando..",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados..",
                "sEmptyTable": "Ninguna tarea disponible en esta tabla",
                "sInfo": "Mostrando un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando un total de 0 registros",
                "sInfoFiltered": "(Filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar: ",
                "sLoadingRecords": "Cargando",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                }
            }
        });
    })

    function mdlRreportePdfProyectos() {
        $("#ModalDescargarPdfProyectos").modal("show");
    }


</script>