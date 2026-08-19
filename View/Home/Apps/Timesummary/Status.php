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
        .fc-event:hover {
            color: #ebebeb !important;
        }
    </style>

    <div class="page-content">
        <div class="container-fluid">

            <!-- Título -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light">
                        <span class="fs-12 badge bg-primary text-light">
                            Status
                            <span class="badge bg-dark text-light" id="client_id_consultar_proyectos"></span>
                        </span>
                    </div>
                </div>
            </div>
            <!-- Fin título -->

            <!-- Contenido principal -->
            <div class="card">
                <table id="tableStatus">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Cliente</th>
                            <th style="text-align: center;">Producto</th>
                            <th style="text-align: center;">Fecha fin</th>
                            <th style="text-align: center;">Sector</th>
                            <th style="text-align: center;">Dimensionamiento</th>
                            <th style="text-align: center;">Avances</th>
                            <th style="text-align: center;">Usuarios</th>
                            <th style="text-align: center;">Estado</th>
                        </tr>
                    </thead>
                </table>
                    <p style="width: 75%; font-size: .8rem; margin: 0 auto;" class="text-danger text-center my-3">La siguiente información corresponde al progreso de carga de horas en <strong>Timessumary</strong> para aquellos proyectos que aún no han completado el <strong>100%</strong> de la carga y cuyo estado es <strong>NUEVO o ABIERTO</strong>.</p>
            </div>
            <!-- Fin contenido principal -->
        </div>
    </div>

    <?php
    include_once __DIR__ . "/../../Public/Template/footer.php";
    ?>
<?php } else {
    header("Location:" . URL);
    exit;
}
?>
<script>
    var url = "<? URL ?>";
    var table;
    // alert(URL);
    document.addEventListener('DOMContentLoaded', function() {
        $.ajax({
            type: "PSOT",
            url: URL + "Controller/ctrTimesummary.php?accion=getProyectosStatus",
            data: "",
            dataType: "json",
            success: function(response) {
                console.log(response);

            },
            error: function(error) {
                console.log(error);

            }
        });

        table = new DataTable('#tableStatus', {
            ajax: {
                url: URL + "Controller/ctrTimesummary.php?accion=getProyectosStatus",
                dataSrc: ''
            },
            columns: [{
                    data: 'client_rs',
                    className: 'text-center'
                },
                {
                    data: 'producto',
                    className: 'text-center'
                },
                {
                    data: 'fech_fin',
                    className: 'text-center'
                },
                {
                    data: 'sector',
                    className: 'text-center'
                },
                {
                    data: 'dimensionamiento',
                    className: 'text-center',
                    render: function(data) {
                        return `${data} hs`
                    }
                },
                {
                    data: 'porcentaje_avance',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
            <div style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                <div class="progress" title="${row.horas_consumidas_total}hs" style="height: 15px; flex: 1; min-width: 120px; cursor: text;">
                    <div class="progress-bar bg-info" role="progressbar" 
                         style="width: ${data}%;" 
                         aria-valuenow="${data}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
                <span style="min-width: 35px; font-weight: bold;">${data}%</span>
            </div>`;
                    }
                },
                {
                    data: 'usuarios_asignados',
                    className: 'text-center',
                    render: function(data, type, row) {
                        const usuarios = data.split(', ');
                        // Dividir cada 3 usuarios
                        let resultado = '';
                        for (let i = 0; i < usuarios.length; i += 3) {
                            resultado += usuarios.slice(i, i + 3).join(', ') + '<br>';
                        }
                        return `<span style="display: inline-block;">${resultado}</span>`;
                    }
                }, {
                    data: 'estado',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<span class="badge" style="background-color: ${row.estado_color}; 
                           color: white; 
                           padding: .3rem .5rem; 
                           display: inline-block;">
                ${data}
                </span>`;
                    }
                }
            ]
        });
    });
</script>