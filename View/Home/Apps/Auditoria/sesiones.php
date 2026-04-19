<?php
require_once __DIR__ . "/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../Model/Clases/Headers.php";
    Headers::get_cors();
    include_once __DIR__ . "/../../Public/Template/head.php";
    include_once __DIR__ . "/../../Public/Template/main_content.php";


?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light bg-light">
                        <span class="fs-12 badge bg-primary text-light">Auditoria de Sessiones</span>

                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>


        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <table id="tablaUsuariosSesiones">
                        <thead>
                            <tr>
                                <th style="width: 40%; text-align: center;">Usuario</th>
                                <th style="width: 15%; text-align: center;">Sector</th>
                                <th style="width: 15%; text-align: center;">Fecha</th>
                                <th style="width: 10%; text-align: center;">Evento</th>
                                <th style="width: 10%; text-align: center;">Estado Usuario</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
        tabla = $("#tablaUsuariosSesiones").dataTable({
            "ajax": {
                url: URL + "Controller/ctrAuditoria.php?case=get_audit_sesiones",
                type: "post",
                dataType: "json",
                error: function(e) {}
            },

            "order": [
                [5, "desc"]
            ],

            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 10,
            "autoWidth": false,
            "columnDefs": [{
                "className": "text-center",
                "targets": "_all"
            }],
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
</script>