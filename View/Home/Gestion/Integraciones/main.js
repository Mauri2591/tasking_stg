document.addEventListener("DOMContentLoaded", () => {
    var tabla;

    tabla = $("#table_integraciones_api_keys").dataTable({
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
            url: "../../../../Controller/ctrIntegraciones.php?case=get_api_keys",
            type: "post",
            dataType: "json",
            data: {
                // usu_sector: 1
            },
            error: function (e) {}
        },
        "order": [
            [0, "desc"]
        ], //Ordenar descendentemente
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7, //cantidad de tuplas o filas a mostrar
        "autoWith": false,
        "language": {
            "sProcessing": "Procesando..",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados..",
            "sEmptyTable": "Ningun registro disponible en esta tabla",
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

    $.post("../../../../Controller/ctrIntegraciones.php?case=get_herramientas",
        function (data, textStatus, jqXHR) {
            $("#combo_herramienta").html(data)
        },
        "html"
    );

    $.post("../../../../Controller/ctrProyectos.php?proy=get_sectores",
        function (data, textStatus, jqXHR) {
            $("#combo_sector").html(data)
        },
        "html"
    );

    $("#btnCrearApiKey").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "../../../../Controller/ctrIntegraciones.php?case=get_api_key",
            data: {
                sector_id: $("#combo_sector").val(),
                id_herramienta: $("#combo_herramienta").val()
            },
            dataType: "json",
            success: function (response) {

                setTimeout(() => {
                    if ($.fn.DataTable.isDataTable('#table_integraciones_api_keys')) {
                        $('#table_integraciones_api_keys').DataTable().ajax.reload(null, false);
                    }
                }, 500);

                Swal.fire({
                    icon: "success",
                    title: "Bien",
                    text: "Creado correctamente",
                    timer: 1000,
                    showConfirmButton: false,
                    showCancelButton: false
                });
            },
            error: function (error) {
                Swal.fire({
                    icon: "warning",
                    title: "Error",
                    text: "Ya posee una Api Key activa",
                    showConfirmButton: true,
                    showCancelButton: false
                });
            }
        });
    });
});

function inactivarApiKey(id) {
    Swal.fire({
        icon: "warning",
        title: "Atencion",
        text: "Desea eliminar esta Api Key?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../../../Controller/ctrIntegraciones.php?case=inhabilitar_api_keys", {
                    id: id
                },
                function (data, textStatus, jqXHR) {

                },
                "json"
            );

            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#table_integraciones_api_keys')) {
                    $('#table_integraciones_api_keys').DataTable().ajax.reload(null, false);
                }
            }, 500);

            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Inhabilitado correctamente",
                timer: 1000,
                showConfirmButton: false,
                showCancelButton: false
            });
        } else {
            return;
        }
    })
}