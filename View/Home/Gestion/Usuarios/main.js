document.addEventListener("DOMContentLoaded", function () {
    var tabla;
    const checkResetClave = document.getElementById("checkResetClave");
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
            error: function (e) {}
        },
        "order": [
            [5, "desc"]
        ], //Ordenar descendentemente
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10, //cantidad de tuplas o filas a mostrar
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
        formData.append('usu_nom', document.getElementById('nombre').value);
        formData.append('usu_ape', document.getElementById('apellido').value);
        formData.append('usu_correo', document.getElementById('correo').value);
        formData.append('usu_tel', document.getElementById('usu_tel').value);
        formData.append('sector_id', document.getElementById('combo_usuarios').value);
        return formData;
    }

    document.getElementById("correo").addEventListener("input", function () {
        let valor = this.value.trim();
        let tablaApi = $('#table_usuarios').DataTable();
        tablaApi.search(valor).draw();

        let existeActivo = false;
        if (valor !== "") {
            tablaApi.rows({
                search: 'applied'
            }).every(function () {
                let fila = this.data();
                let correo = $('<div>').html(fila[1]).text().trim().toLowerCase(); // col 1 = CORREO
                let estado = $('<div>').html(fila[4]).text().trim().toLowerCase(); // col 4 = ESTADO
                if (correo === valor.toLowerCase() && estado === "activo") {
                    existeActivo = true;
                }
            });
        }

        let input = this;
        input.classList.toggle("is-invalid", existeActivo); // rojo solo si existe ACTIVO
        input.classList.toggle("is-valid", valor !== "" && !existeActivo); // verde si libre o solo inactivo
    });

    $("#btnIngresarUsuario").on("click", function (e) {
        e.preventDefault();
        let formData = get_datos_ajax();
        $.ajax({
            type: "POST",
            url: "../../../../Controller/ctrUsuarios.php?usuarios=insert_usuario",
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                $('#table_usuarios').DataTable().ajax.reload();
                Swal.fire({
                    icon: "success",
                    title: "Usuario creado correctamente",
                    timer: 1200,
                    showConfirmButton: false
                });
                document.getElementById("form_insert_usuario").reset();

                // limpiar estado visual y filtro
                let correo = document.getElementById("correo");
                correo.classList.remove("is-valid", "is-invalid");
                $('#table_usuarios').DataTable().search('').draw();
            },
            error: function (xhr) {
                let mje = "Datos vacios!";
                try {
                    let resp = xhr.responseJSON || JSON.parse(xhr.responseText);
                    if (resp && resp.error) mje = resp.error; // usuarios usa la clave "error"
                } catch (e) {}

                let htmlmje = `<div id="mje_campos_obligatorios_vacios_insert_usuario" class="alert alert-warning text-center" role="alert">
                            <a class="alert-link">Error! <br></a>${mje}
                        </div>`;

                document.getElementById("cont_mje_campos_obligatorios_vacios_insert_usuario").innerHTML = htmlmje;
                document.getElementById("correo").focus();
                setTimeout(() => {
                    let el = document.getElementById("mje_campos_obligatorios_vacios_insert_usuario");
                    if (el) el.remove();
                }, 2000);
            }
        });
    });

    if (checkResetClave) {
        checkResetClave.addEventListener("change", function () {
            if (this.checked) {
                $("#usu_pass_editar").prop("disabled", false);
            } else {
                $("#usu_pass_editar").prop("disabled", true);
            }
        })
    }
})

function editar_usuario(id) {
    $.post("../../../../Controller/ctrUsuarios.php?usuarios=get_usuario_x_id_editar_desde_calidad", {
            usu_id: id
        },
        function (data, textStatus, jqXHR) {
            let option = '';
            console.log(data);
            checkResetClave.checked = false;
            $("#usu_pass_editar").val('');
            $("#usu_pass_editar").prop("disabled", true);

            if (data.est == 1) {
                option = `
                <option selected value="1" class="bg-success text-light fw-bold">Activo</option>
                <option value="0" style="background-color:gray" class="text-light fw-bold">Inactivo</option>
            `;
            } else {
                option = `
                <option selected value="0" style="background-color:gray" class="text-light fw-bold">Inactivo</option>
                <option value="1" class="bg-success text-light fw-bold">Activo</option>
            `;
            }
            $("#editar_estado_usuario").html(option)
            $("#modal_editar_usuario").modal("show");
            $("#usu_id_editar").val(id);
            $("#usu_nom_editar").val(data.usu_nom);
            $("#usu_correo_editar").val(data.usu_correo);
            $("#usu_ape_editar").val(data.usu_ape);
        },
        "json"
    );
}

function boton_editar_usuario() {
    $.ajax({
        type: "POST",
        url: "../../../../Controller/ctrUsuarios.php?usuarios=editar_usuario_desde_calidad",
        data: {
            usu_id: $("#usu_id_editar").val(),
            usu_nom: $("#usu_nom_editar").val(),
            usu_ape: $("#usu_ape_editar").val(),
            usu_correo: $("#usu_correo_editar").val(),
            usu_pass: $("#usu_pass_editar").val(),
            est: $("#editar_estado_usuario").val()

        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            Swal.fire({
                icon: "success",
                title: "Usuario editado correctamente",
                timer: 1200,
                showConfirmButton: false
            }).then(() => {
                $("#modal_editar_usuario").modal("hide");
                $('#table_usuarios').DataTable().ajax.reload(null, false);
            });
        }
    });
}