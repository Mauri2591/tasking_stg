$(document).ready(function () {

        tabla = $("#table_proyectos_pre_nuevos_eh").dataTable({
        "aProcessing": true,
        "aServerSide": true,
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_nuevos_vista_calidad",
            type: "post",
            dataType: "json",
            data: {
                sector_id: 1,
                estados_id: 26
            },
            error: function (e) {}
        },
        "order": [
        ], 
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7, //cantidad de tuplas o filas a mostrar
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

    tabla = $("#table_proyectos_nuevos_eh").dataTable({
        "aProcessing": true,
        "aServerSide": true,
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_nuevos_vista_calidad",
            type: "post",
            dataType: "json",
            data: {
                sector_id: 1,
                estados_id: 1
            },
            error: function (e) {}
        },
        "order": [
        ], 
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7, //cantidad de tuplas o filas a mostrar
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


    tabla = $("#table_proyectos_abiertos_eh").dataTable({
        "aProcessing": true,
        "aServerSide": true,
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_nuevos_vista_calidad",
            type: "post",
            dataType: "json",
            data: {
                sector_id: 1,
                estados_id: 2
            },
            error: function (e) {}
        },
        "order": [
        ], 
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7, //cantidad de tuplas o filas a mostrar
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

    tabla = $("#table_proyectos_realizados_eh").dataTable({
        "aProcessing": true,
        "aServerSide": true,
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_nuevos_vista_calidad",
            type: "post",
            dataType: "json",
            data: {
                sector_id: 1,
                estados_id: 3
            },
            error: function (e) {}
        },
        "order": [
        ], 
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7, //cantidad de tuplas o filas a mostrar
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
});

function recargar_tablas(tablas) {
    tablas.forEach(function (id) {
        if ($.fn.DataTable.isDataTable(id)) {
            $(id).DataTable().ajax.reload(null, false);
        }
    });
}

function cambiar_estado_proy_desde_calidad_a_borrador(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea pasar el proyecto a Borrador?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.when(
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                id: id_proyecto_gestionado,
                estados_id: 14
            }),
            $.post("../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                id_proyecto_gestionado: id_proyecto_gestionado,
                estados_id: 14
            })
        ).done(function () {
            recargar_tablas(['#table_proyectos_nuevos_eh','#table_proyectos_pre_nuevos_eh', '#table_proyectos_borrador']);
            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Borrador!",
                timer: 1500,
                showConfirmButton: false
            });
        }).fail(function () {
            Swal.fire({ icon: "error", title: "Error", text: "No se pudo actualizar el proyecto" });
        });
    });
}

function cambiar_estado_proy_desde_calidad_a_nuevo(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea pasar el proyecto a Abierto?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.when(
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                id: id_proyecto_gestionado,
                estados_id: 1
            }),
            $.post("../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                id_proyecto_gestionado: id_proyecto_gestionado,
                estados_id: 1
            })
        ).done(function () {
            recargar_tablas(['#table_proyectos_pre_nuevos_eh', '#table_proyectos_nuevos_eh']);
            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Nuevo!",
                timer: 1500,
                showConfirmButton: false
            });
        }).fail(function () {
            Swal.fire({ icon: "error", title: "Error", text: "No se pudo actualizar el proyecto" });
        });
    });
}

function cambiar_estado_proy_desde_calidad_a_abierto(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea pasar el proyecto a Abierto?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.when(
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                id: id_proyecto_gestionado,
                estados_id: 2
            }),
            $.post("../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                id_proyecto_gestionado: id_proyecto_gestionado,
                estados_id: 2
            })
        ).done(function () {
            recargar_tablas(['#table_proyectos_nuevos_eh', '#table_proyectos_abiertos_eh']);
            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Nuevo!",
                timer: 1500,
                showConfirmButton: false
            });
        }).fail(function () {
            Swal.fire({ icon: "error", title: "Error", text: "No se pudo actualizar el proyecto" });
        });
    });
}

function cerrar_proyecto(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea cerrar el proyecto?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.when(
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                id: id_proyecto_gestionado,
                estados_id: 4
            }),
            $.post("../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                id_proyecto_gestionado: id_proyecto_gestionado,
                estados_id: 4
            })
        ).done(function () {
            recargar_tablas(['#table_proyectos_realizados_eh']);
            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Nuevo!",
                timer: 1500,
                showConfirmButton: false
            });
        }).fail(function () {
            Swal.fire({ icon: "error", title: "Error", text: "No se pudo actualizar el proyecto" });
        });
    });
}

function ver_hosts_eh(id_proyecto_gestionado) {
    $("#ModalVerHosts").modal("show");
    $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_ip", {
            id_proyecto_gestionado: id_proyecto_gestionado
        },
        function (data, textStatus, jqXHR) {
            $("#cont_ip").html(data)
        },
        "html"
    );
    $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_url", {
            id_proyecto_gestionado: id_proyecto_gestionado
        },
        function (data, textStatus, jqXHR) {
            $("#cont_url").html(data)
        },
        "html"
    );
    $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_otro", {
            id_proyecto_gestionado: id_proyecto_gestionado
        },
        function (data, textStatus, jqXHR) {
            $("#cont_otro").html(data)
        },
        "html"
    );
}