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
                        <span class="fs-12 badge bg-primary text-light">Logs Sesiones</span>

                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>

        <div class="col-xl-12">
            <div class="card crm-widget">
                <div style="display: flex; justify-content: end; height: 1.5rem; margin: .5rem;">
                    <span class="mb-4"><span class="badge bg-primary fs-12">Reporte Logs Sesiones</span><i onclick="mdlReportePdfSesiones()" class="ri-file-pdf-fill text-danger fs-22" type="button" title="Descargar documento PDF"></i></span>
                </div>
                <div class="card-body p-0">
                    <table id="tablaUsuariosSesiones" class="table table-bordered table-hover w-100">
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
    <?php include_once __DIR__ . "/Modals/mdlSesiones.php"; ?>
    <?php
    include_once __DIR__ . "/../../Public/Template/footer.php";
    ?>
<?php } else {
    header("Location:" . URL);
    exit;
}
?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
<script>
    var tabla;
    var URL = "<?php echo URL ?>";
    const modalDescargar = document.getElementById('ModalDescargarPdfSesiones');
    const formReporte = modalDescargar?.querySelector('form');

    document.addEventListener("DOMContentLoaded", function() {
        if (modalDescargar) {
            modalDescargar.addEventListener('show.bs.modal', () => {
                const hoy = new Date();
                const año = hoy.getFullYear();
                const mes = String(hoy.getMonth() + 1).padStart(2, '0');

                const primerDia = `${año}-${mes}-01`;
                const ultimoDia = new Date(año, hoy.getMonth() + 1, 0);
                const ultimoDiaFormato = ultimoDia.toISOString().split('T')[0];

                document.getElementById('inputDesde').value = primerDia;
                document.getElementById('inputHasta').value = ultimoDiaFormato;
            });
        }

        if (formReporte) {
            formReporte.addEventListener('submit', (e) => {
                const desde = new Date(document.getElementById('inputDesde').value);
                const hasta = new Date(document.getElementById('inputHasta').value);

                const diffMeses = (hasta.getFullYear() - desde.getFullYear()) * 12 +
                    (hasta.getMonth() - desde.getMonth());

                if (diffMeses > 1) {
                    e.preventDefault();
                    Swal.fire({
                        icon: "warning",
                        title: "Atención",
                        text: "La descarga está limitada a 1 mes máximo",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    return false;
                }
            });
        }
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



    });

    function mdlReportePdfSesiones() {
        $("#ModalDescargarPdfSesiones").modal("show");
        // window.location.href= URL+"Controller/ctrReportes.php?case=get_audit_sesiones_x_fecha&"+'desde='+'2026-04-10&'+'hasta='+'2026-04-20'
    }
</script>