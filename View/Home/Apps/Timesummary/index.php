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

            <!-- Título -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">
                            Calendario
                            <span class="badge bg-dark text-light" id="client_id_consultar_proyectos"></span>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- Fin título -->

            <div class="row" style="gap: 1rem;">

                <div class="col-lg-7"
                    style="border:.1rem solid gainsboro; padding:1rem; border-radius: 6px; background:#fff;">
                    <div id="calendar"></div>
                </div>

                <div class="col-lg-4"
                    style="
                background: #fff; 
                border:.1rem solid gainsboro; 
                border-radius: 6px; 
                padding: .8rem;
                height: 100%;">
                    <div style="display: flex; justify-content: space-between;">
                        <div id="caption_tareas">

                        </div>
                        <i id="btnVerHistorialTimesummary" type="button" title="Ver todos" class="ri-history-line" style="font-size: 1.2rem;"></i>
                    </div>
                    <div style="display: flex; justify-content: end;">
                        <input id="id_tareas_x_similitud" type="text" style="width: 50%;" class="form-control form-control-sm">
                    </div>
                    <table
                        style="
                    margin-top: 1rem;
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                    text-align: center;
                    font-size: 0.85rem;
                    color: #333;
                    border: 1px solid gainsboro;
                    border-radius: 6px;">
                        <thead style="background: #f8f9fa; font-weight: bold;">
                            <tr>
                                <th style="width: 75%; border: 1px solid gainsboro;">Proyecto</th>
                                <th style="width: 25%; border: 1px solid gainsboro;">Producto</th>
                                <th style="width: 20%; border: 1px solid gainsboro;">Hs</th>
                                <th style="width: 15%; border: 1px solid gainsboro;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody_tabla_timasummary" style="border-top: 1px solid gainsboro; font-size:.7rem">
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Fin contenido principal -->
        </div>
    </div>
    <?php
    include_once __DIR__ . "/Modales/mdlCarga.php";
    include_once __DIR__ . "/Modales/mdlHostorial.php";
    include_once __DIR__ . "/../../Public/Template/footer.php";
    ?>
<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
}
?>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
<script>
    let huboUpdate = false;
    document.addEventListener('DOMContentLoaded', function() {
        var tabla;
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            events: '../../../../Controller/ctrTimesummary.php?accion=get_tareas',
            selectable: true,
            Boolean,
            default: true,
            selectMirror: true,
            dateClick: function(info) {
                let FECHA = info.dateStr;
                $("#mdlCarcaTimesummary").modal("show");
                $("#fechaSeleccionada").text(FECHA)

                $.post("../../../../Controller/ctrTimesummary.php?accion=get_titulos_proyectos",
                    function(data, textStatus, jqXHR) {
                        $("#id_proyecto_gestionado").html(data)
                    },
                    "html"
                );

                $.post("../../../../Controller/ctrTimesummary.php?accion=get_producto_proyectos",
                    function(data, textStatus, jqXHR) {
                        $("#id_producto").html(data)

                    },
                    "html"
                );

                $.post("../../../../Controller/ctrTimesummary.php?accion=get_tareas_total",
                    function(data, textStatus, jqXHR) {

                        $("#id_tarea").html(data)
                    },
                    "html"
                );

                $("#btnGuardarTarea").off("click").on("click", function(e) {
                    e.preventDefault();

                    let FECHA = info.dateStr;
                    let horaDesde = $("#hora_desde").val();
                    let horaHasta = $("#hora_hasta").val();

                    let nuevoInicio = new Date(`${FECHA}T${horaDesde}`);
                    let nuevoFin = new Date(`${FECHA}T${horaHasta}`);

                    // Buscar si hay eventos que se solapan
                    let existeConflicto = calendar.getEvents().some(evento => {
                        let inicio = evento.start;
                        let fin = evento.end;

                        // mismo día y horarios que se cruzan
                        return (
                            inicio.toISOString().split("T")[0] === FECHA &&
                            (
                                (nuevoInicio >= inicio && nuevoInicio < fin) ||
                                (nuevoFin > inicio && nuevoFin <= fin) ||
                                (nuevoInicio <= inicio && nuevoFin >= fin)
                            )
                        );
                    });

                    if (existeConflicto) {
                        Swal.fire({
                            icon: "warning",
                            title: "Error",
                            text: "Ya existe una tarea en ese rango horario.",
                            showConfirmButton: true
                        });
                        return; 
                    }

                    // ✅ Si no hay conflicto, sigue normalmente
                    let data = {
                        id_proyecto_gestionado: $("#id_proyecto_gestionado").val(),
                        id_producto: $("#id_producto").val(),
                        id_tarea: $("#id_tarea").val(),
                        fecha: FECHA,
                        hora_desde: horaDesde,
                        hora_hasta: horaHasta,
                        descripcion: $("#descripcion").val()
                    };

                    $.ajax({
                        type: "POST",
                        url: "../../../../Controller/ctrTimesummary.php?accion=insert_tarea",
                        data: data,
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Bien",
                                text: response.success,
                                showConfirmButton: false,
                                timer: 1100
                            });

                            setTimeout(() => {
                                calendar.refetchEvents();
                                $("#mdlCarcaTimesummary").modal("hide");
                                $("#id_proyecto_gestionado, #id_producto, #id_tarea, #hora_desde, #hora_hasta, #descripcion").val('');
                            }, 500);
                        },
                        error: function(error) {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: error.responseJSON.error,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                });

            },
            eventClick: function(info) {
                const EVENTO = info.event;
                const TITLE = EVENTO.title;
                const EVENTO_ID = EVENTO.id;
                const START = EVENTO.start;
                const END = EVENTO.end;
                const FECHA_OBJ = new Date(START);
                const FECHA_FORMATO = FECHA_OBJ.toISOString().split('T')[0];
                const PRODUCTO = EVENTO.extendedProps.producto;
                const DESCRIPCION = EVENTO.extendedProps.descripcion;
                const ID_TAREA = EVENTO.extendedProps.id_tarea;
                const TAREA = EVENTO.extendedProps.nombre;

                const START_HORA = START.getHours().toString().padStart(2, '0');
                const START_MIN = START.getMinutes().toString().padStart(2, '0');
                const END_HORA = END ? END.getHours().toString().padStart(2, '0') : '';
                const END_MIN = END ? END.getMinutes().toString().padStart(2, '0') : '';

                $("#hora_desde_edit").val(`${START_HORA}:${START_MIN}`);
                $("#hora_hasta_edit").val(END ? `${END_HORA}:${END_MIN}` : '');
                $("#id_editar_proyecto_gestionado").val(TITLE)
                $("#id_editar_producto").val(PRODUCTO)
                $("#id_editar_tarea").val(ID_TAREA)
                $("#editar_descripcion").val(DESCRIPCION)
                $("#fechaSeleccionadaEdit").text(FECHA_FORMATO);
                $("#mdlEditarTimesummary").modal("show");


                $.post("../../../../Controller/ctrTimesummary.php?accion=get_tareas_x_id", {
                        id: ID_TAREA
                    },
                    function(data) {
                        $("#id_editar_tarea").html(data);
                    },
                    "html");

            }
        });

        document.getElementById("btnVerHistorialTimesummary").addEventListener("click", function() {
            $("#mdlHistorialTimesummary").modal("show")
            tabla = $("#tableHistorialTimesummary").dataTable({
                "aProcessing": true,
                "aServerSide": true,
                "ordering": true,
                "lengthChange": false,
                dom: 'Bfrtip',
                "searching": true,
                lenghtChange: false,
                colReorder: true,
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                "ajax": {
                    url: "../../../../Controller/ctrTimesummary.php?accion=get_titulos_proyectos_total",
                    type: "post",
                    dataType: "json",
                    data: {},
                    error: function(e) {}
                },
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 15, //cantidad de tuplas o filas a mostrar
                "autoWith": false,
                "language": {
                    "sProcessing": "Procesando..",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados..",
                    "sEmptyTable": "Ninguna tarea disponible en esta tabla",
                    "sInfo": "Mostrando un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando un total de 0 registros",
                    "sInfoFiltered": "(Filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar: ",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Ùltimo",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ":Activar para ordenar la columna de manera ascendiente",
                        "sSortDescending": ":Activar para ordenar la columna de manera descendiente"
                    }
                }
            });


            // Handler: cuando el modal se oculta
            $('#mdlHistorialTimesummary').on('hidden.bs.modal', function() {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableHistorialTimesummary')) {
                    $('#tableHistorialTimesummary').DataTable().clear().destroy();
                    $('#tableHistorialTimesummary tbody').empty();
                }

                if (huboUpdate) {
                    location.reload(); // solo recarga si se hizo un update
                    huboUpdate = false; // reseteamos la bandera
                }
            });
        });
        calendar.render();
    });

    $.post("../../../../Controller/ctrTimesummary.php?accion=datos_tabla_ts",
        function(data, textStatus, jqXHR) {
            $("#tbody_tabla_timasummary").html(data)
            console.log(data);

        },
        "html"
    );

    $.post("../../../../Controller/ctrTimesummary.php?accion=get_validar_si_hay_tareas_activas",
        function(data, textStatus, jqXHR) {
            $("#caption_tareas").html(data)

        },
        "html"
    );

    document.getElementById("id_tareas_x_similitud").addEventListener("input", function() {
        if (this.value == '') {
            $.post("../../../../Controller/ctrTimesummary.php?accion=datos_tabla_ts",
                function(data, textStatus, jqXHR) {
                    $("#tbody_tabla_timasummary").html(data)
                },
                "html"
            );
        } else {
            $.post("../../../../Controller/ctrTimesummary.php?accion=get_titulos_proyectos_like", {
                    titulo: $("#id_tareas_x_similitud").val()
                },
                function(data, textStatus, jqXHR) {
                    $("#tbody_tabla_timasummary").html(data)
                },
                "html"
            );
        }

    })

    function editarTarea(id_timesummary_estados) {
        Swal.fire({
            title: "¿Desea inactivar esta tarea?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "../../../../Controller/ctrTimesummary.php?accion=cambiar_estado_tarea",
                    data: {
                        id_timesummary_estados: id_timesummary_estados,
                        est: 0
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            icon: "success",
                            title: "Bien",
                            text: data.Success || "Tarea inactivada correctamente",
                            showConfirmButton: false,
                            timer: 1300
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1300);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: xhr.responseJSON?.Error || "No se pudo cambiar el estado",
                            showConfirmButton: true
                        });
                        console.error("Error AJAX:", xhr.responseText);
                    }
                });

            }
        });
    }

    function cambiarEstadoTareaHistorial(id_timesummary_estados) {
        $.post("../../../../Controller/ctrTimesummary.php?accion=get_estado_tarea", {
                id_timesummary_estados: id_timesummary_estados
            },
            function(data, textStatus, jqXHR) {
                const ESTADO = data.estado;
                if (ESTADO == '0') {
                    Swal.fire({
                        title: "¿Desea activar esta tarea?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "POST",
                                url: "../../../../Controller/ctrTimesummary.php?accion=cambiar_estado_tarea",
                                data: {
                                    id_timesummary_estados: id_timesummary_estados,
                                    est: 1
                                },
                                dataType: "json",
                                success: function(data) {
                                    huboUpdate = true; // marcamos que hubo un cambio real
                                    Swal.fire({
                                        icon: "success",
                                        title: "Bien",
                                        text: data.Success || "Tarea activada correctamente",
                                        showConfirmButton: false,
                                        timer: 1300
                                    });
                                    setTimeout(() => {
                                        if ($.fn.DataTable.isDataTable('#tableHistorialTimesummary')) {
                                            $('#tableHistorialTimesummary').DataTable().ajax.reload(null, false);
                                        }
                                    }, 500);
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: xhr.responseJSON?.Error || "No se pudo cambiar el estado",
                                        showConfirmButton: true
                                    });
                                    console.error("Error AJAX:", xhr.responseText);
                                }
                            });

                        }
                    });
                } else {
                    Swal.fire({
                        title: "¿Desea inactivar esta tarea?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "POST",
                                url: "../../../../Controller/ctrTimesummary.php?accion=cambiar_estado_tarea",
                                data: {
                                    id_timesummary_estados: id_timesummary_estados,
                                    est: 0
                                },
                                dataType: "json",
                                success: function(data) {
                                    huboUpdate = true; // marcamos que hubo un cambio real
                                    Swal.fire({
                                        icon: "success",
                                        title: "Bien",
                                        text: data.Success || "Tarea inactivada correctamente",
                                        showConfirmButton: false,
                                        timer: 1300
                                    });
                                    setTimeout(() => {
                                        if ($.fn.DataTable.isDataTable('#tableHistorialTimesummary')) {
                                            $('#tableHistorialTimesummary').DataTable().ajax.reload(null, false);
                                        }
                                    }, 500);
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: xhr.responseJSON?.Error || "No se pudo cambiar el estado",
                                        showConfirmButton: true
                                    });
                                    console.error("Error AJAX:", xhr.responseText);
                                }
                            });

                        }
                    });
                }
            },
            "json"
        );
    }
</script>