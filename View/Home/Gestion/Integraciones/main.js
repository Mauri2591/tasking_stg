document.addEventListener("DOMContentLoaded", () => {
    var tabla;

    tabla = $("#table_integraciones_api_keys").dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lenghtChange: false,
        colReorder: true,
        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
        "ajax": {
            url: "../../../../Controller/ctrIntegraciones.php?case=get_api_keys",
            type: "post",
            dataType: "json",
            data: {},
            error: function (e) {}
        },
        "order": [
            [0, "desc"]
        ],
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 7,
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
        function (data) {
            $("#combo_herramienta").html(data);
        }, "html"
    );

    $.post("../../../../Controller/ctrProyectos.php?proy=get_sectores",
        function (data) {
            $("#combo_sector").html(data);
        }, "html"
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
                    showConfirmButton: false
                });
            },
            error: function () {
                Swal.fire({
                    icon: "warning",
                    title: "Error",
                    text: "Ya posee una Api Key activa"
                });
            }
        });
    });

    // =================== GenAi ===================
    $('#btn_genai_enviar').on('click', function () {
        const api_key = $('#genai_api_key').val().trim();
        const modelo = $('#genai_agente').val().trim() || $('#genai_modelo').val();
        const prompt = $('#genai_prompt').val().trim();

        if (!prompt) return Swal.fire('Atención', 'Ingresá un prompt', 'warning');

        $('#genai_resultado').hide();
        $('#genai_spinner').show();

        $.post('../../../../Controller/ctrIntegraciones.php?case=chat', {
            api_key,
            modelo,
            prompt
        }, function (res) {
            $('#genai_spinner').hide();
            if (res.respuesta) {
                $('#genai_respuesta_texto').text(res.respuesta);
                $('#genai_chat_id').text(res.chat_id ? `chat_id: ${res.chat_id}` : '');
                $('#genai_resultado').show();
            } else {
                Swal.fire('Error', 'Sin respuesta del modelo', 'error');
            }
        }, 'json').fail(function () {
            $('#genai_spinner').hide();
            Swal.fire('Error', 'No se pudo conectar', 'error');
        });
    });

    // =================== Consulta Modelos ===================
    $('#btn_cm_consultar').on('click', function () {
        const api_key = $('#cm_api_key').val().trim();
        if (!api_key) return Swal.fire('Atención', 'Ingresá la API Key', 'warning');

        $('#cm_resultado').hide();
        $('#cm_spinner').show();

        $.post('../../../../Controller/ctrIntegraciones.php?case=get_models', {
            api_key
        }, function (res) {
            $('#cm_spinner').hide();
            if (!res.modelos || !res.modelos.length) {
                Swal.fire('Error', 'No se encontraron modelos', 'error');
                return;
            }
            const tbody = $('#cm_tabla_body');
            tbody.empty();
            res.modelos.forEach(m => {
                tbody.append(`
        <tr>
            <td><strong>${m.nombre}</strong></td>
            <td>${m.modo ?? '-'}</td>
            <td>${m.descripcion ?? '-'}</td>
            <td>${m.input_cost ?? '-'}</td>
            <td>${m.output_cost ?? '-'}</td>
            <td>
                <button class="btn btn-success btn-sm btn_usar_modelo" data-modelo="${m.nombre}">
                    Usar
                </button>
            </td>
        </tr>
    `);
            });
            $('#cm_resultado').show();
        }, 'json').fail(function () {
            $('#cm_spinner').hide();
            Swal.fire('Error', 'No se pudo conectar', 'error');
        });
    });

    $(document).on('click', '.btn_usar_modelo', function () {
        const modelo = $(this).data('modelo');
        const api_key = $('#cm_api_key').val().trim();

        $('#genai_agente').val(modelo);
        $('#genai_api_key').val(api_key);

        const tabTest = document.querySelector('a[href="#testGenAi"]');
        if (tabTest) new bootstrap.Tab(tabTest).show();
    });

}); 
// fin DOMContentLoaded

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
                    id
                },
                function () {},
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
                showConfirmButton: false
            });
        }
    });
}