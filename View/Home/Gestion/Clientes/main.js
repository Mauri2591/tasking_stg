$(document).ready(function () {
    var tabla;
    tabla = $("#table_clientes").dataTable({
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
            url: "../../../../Controller/ctrClientes.php?cliente=total",
            type: "post",
            dataType: "json",
            data: {
                usu_sector: 1
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

    $.post("../../../../Controller/ctrProyectos.php?proy=get_combo_prioridad_proy_nuevo_eh",
        function (data, textStatus, jqXHR) {
            $("#combo_prioridad_proy_nuevo").html(data);

            $("#combo_prioridad_proy_nuevo").on("change", function () {
                const $combo = $(this);
                $combo.removeClass("border-success border-warning border-danger");

                switch (this.value) {
                    case "1":
                        $combo.addClass("border-success");
                        break;
                    case "2":
                        $combo.addClass("border-warning");
                        break;
                    case "3":
                        $combo.addClass("border-danger");
                        break;
                    default:
                        $combo.addClass("border-success"); // por defecto
                        break;
                }
            });
        },
        "html"
    );

    function get_datos_insert_cliente() {
        let formData = new FormData();
        formData.append('client_rs', document.getElementById("client_rs").value);
        formData.append('pais_id', document.getElementById("combo_paises").value);
        formData.append('client_cuit', document.getElementById("client_cuit").value);
        formData.append('client_correo', document.getElementById("client_correo").value);
        formData.append('client_tel', document.getElementById("client_tel").value);
        return formData;
    }

    function ajax_insert_cliente(data) {
        let xhr = new XMLHttpRequest();
        xhr.open(
            "POST",
            "../../../../Controller/ctrClientes.php?cliente=insert_cliente",
            true
        );
        xhr.onload = () => {
            let htmlmje = `<div id="mje_campos_obligatorios_vacios_insert_cliente" class="alert alert-warning text-center" role="alert">
                            <a class="alert-link">Error! <br></a>Datos vacios!
                        </div>`;
            if (xhr.status == 200) {
                Swal.fire({
                    icon: "success",
                    title: "Cliente creado correctamente",
                    showConfirmButton: false,
                    timer: 1500
                });
                document.getElementById("form_insert_cliente").reset();

                setTimeout(() => {
                    if ($.fn.DataTable.isDataTable('#table_clientes')) {
                        $('#table_clientes').DataTable().ajax.reload(null, false);
                    }
                }, 500);

            } else if (xhr.status == 400) {
                document.getElementById("cont_mje_campos_obligatorios_vacios_insert_cliente").innerHTML = htmlmje;
                document.getElementById("client_rs").focus();
                setTimeout(() => {
                    document.getElementById("mje_campos_obligatorios_vacios_insert_cliente").remove();
                }, 2000);
            }
        }
        xhr.send(data);
    }
    document.getElementById("btnIngresarCliente").addEventListener("click", function () {
        let data = get_datos_insert_cliente();
        ajax_insert_cliente(data);
    });

    $.post("../../../../Controller/ctrProyectos.php?proy=get_paises",
        function (data, textStatus, jqXHR) {
            document.getElementById("combo_paises").innerHTML = data;
        },
        "html"
    );

    $.post("../../../../Controller/ctrProyectos.php?proy=get_sectores",
        function (data, textStatus, jqXHR) {
            $("#combo_sector_proy_nuevo").html(data)
        },
        "html"
    );

    $.post("../../../../Controller/ctrProyectos.php?proy=get_combo_categorias_x_sector", { sector_id: 1 },
        function (data, textStatus, jqXHR) {
            $("#combo_categoria_proy_nuevo").html(data)
        },
        "html"
    );
    document.getElementById("combo_sector_proy_nuevo").addEventListener("change", function () {
        $.post("../../../../Controller/ctrProyectos.php?proy=get_combo_categorias_x_sector", { sector_id: this.value },
            function (data, textStatus, jqXHR) {
                $("#combo_categoria_proy_nuevo").html(data)
            },
            "html"
        );
    });


    $.post("../../../../Controller/ctrProyectos.php?proy=get_combo_subcategorias_x_sector", { sector_id: 1 },
        function (data, textStatus, jqXHR) {
            $("#combo_subcategoria_proy_nuevo").html(data)
        },
        "html"
    );

    document.getElementById("combo_sector_proy_nuevo").addEventListener("change", function () {
        $.post("../../../../Controller/ctrProyectos.php?proy=get_combo_subcategorias_x_sector", { sector_id: this.value },
            function (data, textStatus, jqXHR) {
                $("#combo_subcategoria_proy_nuevo").html(data)
            },
            "html"
        );
    });
});


function editCliente(client_id) {
    document.getElementById("client_id_update").value = client_id;
    $.post("../../../../Controller/ctrClientes.php?cliente=get_datos_cliente", { client_id: client_id },
        function (data, textStatus, jqXHR) {
            document.getElementById("client_rs_title_update").innerText = data.client_rs
            document.getElementById("client_rs_update").value = data.client_rs
            document.getElementById("client_cuit_update").value = data.client_cuit
            document.getElementById("client_correo_update").value = data.client_correo
            document.getElementById("client_tel_update").value = data.client_tel

        },
        "json"
    );
    $("#ModalUpdateCliente").modal("show");
}

function data_update_cliente() {
    let formData = new FormData();
    formData.append('client_id', document.getElementById("client_id_update").value);
    formData.append('client_rs', document.getElementById("client_rs_update").value);
    formData.append('client_cuit', document.getElementById("client_cuit_update").value);
    formData.append('client_correo', document.getElementById("client_correo_update").value);
    formData.append('client_tel', document.getElementById("client_tel_update").value);
    return formData;
}

function ajax_update_cliente(data) {
    let xhr = new XMLHttpRequest();
    xhr.open(
        "POST",
        "../../../../Controller/ctrClientes.php?cliente=update_datos_cliente",
        true
    );
    xhr.onload = () => {
        let htmlmje = `<div id="mje_campos_obligatorios_vacios_insert_cliente" class="alert alert-warning text-center" role="alert">
                        <a class="alert-link">Error! <br></a>Campos obligatorios vacios!
                    </div>`;
        if (xhr.status == 200) {
            Swal.fire({
                icon: "success",
                title: "Cliente actualizado correctamente",
                showConfirmButton: false,
                timer: 1300
            });

            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#table_clientes')) {
                    $('#table_clientes').DataTable().ajax.reload(null, false);
                }
            }, 500);

            $("#ModalUpdateCliente").modal("hide");
            document.getElementById("form_update_cliente").reset();

        } else if (xhr.status == 400) {
            document.getElementById("modal_cont_mje_campos_obligatorios_vacios_update_cliente").innerHTML = htmlmje;
            document.getElementById("client_rs").focus();
            setTimeout(() => {
                document.getElementById("mje_campos_obligatorios_vacios_insert_cliente").remove();
            }, 2000);
        }
    }
    xhr.send(data);
}

document.getElementById("btnUpdateCliente").addEventListener("click", function () {
    let data = data_update_cliente();
    ajax_update_cliente(data);
})
function altaProject(client_id) {
    document.getElementById("form_crear_proyecto").reset();
    $("#combo_prioridad_proy_nuevo").removeClass("border-danger");
    $("#combo_prioridad_proy_nuevo").removeClass("border-warning");
    $("#combo_prioridad_proy_nuevo").addClass("border-bg");
    $("#ModalCrearProject").modal("show");

    $.post("../../../../Controller/ctrClientes.php?cliente=get_datos_cliente", { client_id: client_id },
        function (data, textStatus, jqXHR) {
            document.getElementById("client_rs_alta_proy").value = data.client_rs
            document.getElementById("pais_id_carga_proy_hidden").value = data.pais_id
        },
        "json"
    );

    $.post("../../../../Controller/ctrClientes.php?cliente=get_paise_x_id", { client_id: client_id },
        function (data, textStatus, jqXHR) {
            document.getElementById("pais_id_carga_proy").value = data.pais_nombre;
        },
        "json"
    );
    document.getElementById("client_id_hidden").value = client_id;
}

function data_insert_proyecto() {
    let formData = new FormData();
    formData.append('client_id', document.getElementById("client_id_hidden").value);
    formData.append('cantidad_servicios', document.getElementById("combo_cantidad_servicios_proy_nuevo").value);
    return formData;
}

function ajax_inserta_proyecto(data) {
    $.ajax({
        type: "post",
        url: "../../../../Controller/ctrProyectos.php?proy=insert_proyecto",
        data: data,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.Status == "Success") {
                Swal.fire({
                    icon: "success",
                    title: "Proyecto creado correctamente",
                    showConfirmButton: false,
                    timer: 1500
                });
                $("#ModalCrearProject").modal("hide");
                document.getElementById("form_crear_proyecto").reset();
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Datos vacios, complete los campos",
                    showConfirmButton: true
                });
            }
        }
    });
}

$("#btn_crear_proyecto").off().click(function () {
    let data = data_insert_proyecto();
    ajax_inserta_proyecto(data);
});


