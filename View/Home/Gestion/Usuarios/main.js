document.addEventListener("DOMContentLoaded", function () {
    var tabla;
    tabla = $("#table_usuarios").dataTable({
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
            url: "../../../../Controller/ctrUsuarios.php?usuarios=get_usuarios",
            type: "post",
            dataType: "json",
            data: {
                // usu_sector: 1
            },
            error: function (e) {
            }
        },
        "order": [[0, "desc"]], //Ordenar descendentemente
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

    $.post("../../../../Controller/ctrUsuarios.php?usuarios=get_sectores",
        function (data, textStatus, jqXHR) {
            document.getElementById("combo_usuarios").innerHTML = data;
        },
        "html"
    );

    function get_datos_ajax() {
        let formData = new FormData();
        formData.append('usu_nom', document.getElementById("usu_nom").value);
        formData.append('usu_correo', document.getElementById("usu_correo").value);
        formData.append('usu_tel', document.getElementById("usu_tel").value);
        formData.append('sector_id', document.getElementById("combo_usuarios").value);
        return formData;
    }


    let htmlmje = `<div id="mje_campos_obligatorios_vacios_insert_usuario" class="alert alert-warning text-center" role="alert">
    <a class="alert-link">Error! <br></a>Hay campos vacíos
        </div>`;

    $("#btnIngresarUsuario").off().click("click", function () {
        $.ajax({
            type: "POST",
            url: "../../../../Controller/ctrUsuarios.php?usuarios=insert_usuario",
            data: get_datos_ajax(),
            contentType: false,
            processData: false,
            success: function (response) {
                $('#table_usuarios').DataTable().ajax.reload();
                Swal.fire({
                    icon: "success",
                    title: "Usuario creado correctamente",
                    showConfirmButton: false,
                    timer: 1300
                });
                document.getElementById("form_insert_usuario").reset();
            },
            error: function (err) {
                document.getElementById("cont_mje_campos_obligatorios_vacios_insert_usuario").innerHTML = htmlmje;
                setTimeout(() => {
                    document.getElementById("mje_campos_obligatorios_vacios_insert_usuario")?.remove();
                }, 1300);
            }
        });
    });

})

