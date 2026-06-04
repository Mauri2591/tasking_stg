var tabla;
var VALIDAR_SI_HAY_FECHA_INICIO = false;

//***************  Borradores  *****************************
$(document).ready(function () {

    let SECTOR_ID = null;
    let CATEGORIA_ID = null;
    let TITULO_EDITADO_MANUAL = false;

    tabla = $("#table_proyectos_borrador").DataTable({
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_nuevos_borrador",
            type: "post",
            dataType: "json",
            data: {
                // usu_sector: 1
            },
            error: function (e) {}
        },
        "order": [
            [0, "asc"]
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

    tabla = $("#table_proyectos_total").DataTable({
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_nuevos_borrador",
            type: "post",
            dataType: "json",
            data: {
                // usu_sector: 1
            },
            error: function (e) {}
        },
        "order": [
            [0, "asc"]
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

    tabla = $("#table_proyectos_total_calidad").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ordering": true, // ✅ respetar el ORDER BY del SQL
        "lengthChange": false, // ✅ corregido el typo
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_total",
            type: "post",
            dataType: "json",
            data: {
                // usu_sector: 1
            },
            error: function (e) {}
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 9, //cantidad de tuplas o filas a mostrar
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

    tabla = $("#table_proyectos_realizados").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ordering": true, // ✅ respetar el ORDER BY del SQL
        "lengthChange": false, // ✅ corregido el typo
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_realizados_vista_calidad",
            type: "post",
            dataType: "json",
            data: {
                estados_id: 3
            },
            error: function (e) {}
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 9, //cantidad de tuplas o filas a mostrar
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

    tabla = $("#table_cross_sell_sectores").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ordering": true, // ✅ respetar el ORDER BY del SQL
        "lengthChange": false, // ✅ corregido el typo
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
            url: "../../../../../Controller/ctrProyectos.php?proy=getClientesConSectorSinContratar",
            type: "post",
            dataType: "json",
            data: {
                estados_id: 3
            },
            error: function (e) {}
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 9, //cantidad de tuplas o filas a mostrar
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

    tabla = $("#table_proyectos_en_proceso").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ordering": true, // ✅ respetar el ORDER BY del SQL
        "lengthChange": false, // ✅ corregido el typo
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_en_proceso_vista_calidad",
            type: "post",
            dataType: "json",
            data: {},
            error: function (e) {}
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 9, //cantidad de tuplas o filas a mostrar
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

    tabla = $("#table_proyectos_recurrencia").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "ordering": true, // ✅ respetar el ORDER BY del SQL
        "lengthChange": false, // ✅ corregido el typo
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
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_recurrentes",
            type: "post",
            dataType: "json",
            data: {},
            error: function (e) {}
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 9, //cantidad de tuplas o filas a mostrar
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

    const elUsuariosSector = document.getElementById('usuarios_sector');
    if (elUsuariosSector) {
        elUsuariosSector.addEventListener('change', function () {
            const checks = document.querySelectorAll('#combo_usuario_x_sector input[type="checkbox"]');
            checks.forEach(check => check.checked = this.checked);
        });
    }
    const elBtnRechequeo = document.getElementById("btn_rechequeo");
    if (elBtnRechequeo) {
        elBtnRechequeo.addEventListener("click", () => {
            $("#ModalRechequeo").modal("show");
            tabla = $("#table_para_rechequeo").DataTable({
                "aProcessing": true,
                "aServerSide": true,
                "ordering": true, // ✅ respetar el ORDER BY del SQL
                "lengthChange": false, // ✅ corregido el typo
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
                    url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_en_proceso_vista_calidad",
                    type: "post",
                    dataType: "json",
                    data: {},
                    error: function (e) {}
                },
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 9, //cantidad de tuplas o filas a mostrar
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
    }
});

$("#combo_sector_proy_nuevo").change(function (e) {
    // e.preventDefault();
    switch (this.value) {
        case '1': //EH
            document.getElementById('container_ips_urls').innerHTML =
                `<div class="col-sm-3 mr-1">
                <div class="mb-3">
                    <span class="badge bg-light fs-10 mb-1 text-dark">Ip's</span>
                    <input type="hidden" hidden value="IP">
                    <textarea class="form-control" id="ips_proy_nuevo_eh" rows="3"
                        placeholder="Engrese las Ips"></textarea>
                </div>
                    <div id="mje_ips_proy_nuevo_eh">

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Url's</span>
                        <input type="hidden" hidden value="URL">
                        <textarea class="form-control" id="urls_proy_nuevo_eh" rows="3"
                            placeholder="Ingrese las URL's"></textarea>
                    </div>
                    <div id="mje_urls_proy_nuevo_eh">
                    </div>
                </div>
                <div class="col-sm-3 ml-1">
                    <div class="mb-3">
                        <input type="hidden" hidden value="OTROS">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Otros activos</span>

                        <textarea class="form-control" id="otros_proy_nuevo" rows="3"
                            placeholder="Otros"></textarea>
                    </div>
                    <div id="mje_urls_proy_nuevo_otros">
                    </div>
                </div>`;
            activarValidacionTextarea(
                "ips_proy_nuevo_eh",
                "mje_ips_proy_nuevo_eh",
                "IP"
            );

            activarValidacionTextarea(
                "urls_proy_nuevo_eh",
                "mje_urls_proy_nuevo_eh",
                "URL"
            );
            break;

        case '2': //SOC
            document.getElementById('container_ips_urls').innerHTML =
                `<div class="col-sm-3 mr-1">
                <div class="mb-3">
                    <span class="badge bg-light fs-10 mb-1 text-dark">Dispositivos</span>
                    <input type="hidden" hidden value="DISPOSITIVOS">
                    <textarea class="form-control" id="dispositivos_proy_nuevo_soc" rows="3"
                        placeholder="Engrese dispositivos"></textarea>
                </div>
                    <div id="mje_dispositivos_proy_nuevo_soc">

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Agentes</span>
                        <input type="hidden" hidden value="AGENTES">
                        <textarea class="form-control" id="agentes_proy_nuevo_soc" rows="3"
                            placeholder="Ingrese los agentes"></textarea>
                    </div>
                    <div id="mje_agentes_proy_nuevo_soc">
                    </div>
                </div>
                <div class="col-sm-3 ml-1">
                    <div class="mb-3">
                        <input type="hidden" hidden value="OTROS">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Otros activos</span>

                        <textarea class="form-control" id="otros_proy_nuevo_soc" rows="3"
                            placeholder="Otros"></textarea>
                    </div>
                    <div id="mje_urls_proy_nuevo_otros">
                    </div>
                </div>`;
            break;

        case '3': //SASE
            document.getElementById('container_ips_urls').innerHTML =
                `<div class="col-sm-3 mr-1">
                <div class="mb-3">
                    <span class="badge bg-light fs-10 mb-1 text-dark">Ip's</span>
                    <input type="hidden" hidden value="IP">
                    <textarea class="form-control" id="ips_proy_nuevo_eh" rows="3"
                        placeholder="Engrese las Ips"></textarea>
                </div>
                    <div id="mje_ips_proy_nuevo_eh">

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Equipos</span>
                        <input type="hidden" hidden value="EQUIPOS">
                        <textarea class="form-control" id="equipos_proy_nuevo_sase" rows="3"
                            placeholder="Ingrese los equipos"></textarea>
                    </div>
                    <div id="mje_equipos_proy_nuevo_sase">
                    </div>
                </div>
                <div class="col-sm-3 ml-1">
                    <div class="mb-3">
                        <input type="hidden" hidden value="OTROS">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Otros activos</span>

                        <textarea class="form-control" id="otros_proy_nuevo_sase" rows="3"
                            placeholder="Otros"></textarea>
                    </div>
                    <div id="mje_urls_proy_nuevo_otros">
                    </div>
                </div>`;
            break;

        case '4': //CALIDAD
            document.getElementById('container_ips_urls').innerHTML =
                `<div class="col-sm-3 mr-1">
           
                </div>`;
            break;

        case '5': //IR
            document.getElementById('container_ips_urls').innerHTML =
                `<div class="col-sm-3 mr-1">
                <div class="mb-3">
                    <span class="badge bg-light fs-10 mb-1 text-dark">Ip's</span>
                    <input type="hidden" hidden value="IP">
                    <textarea class="form-control" id="ips_proy_nuevo_eh" rows="3"
                        placeholder="Engrese las Ips"></textarea>
                </div>
                    <div id="mje_ips_proy_nuevo_eh">

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Url's</span>
                        <input type="hidden" hidden value="URL">
                        <textarea class="form-control" id="urls_proy_nuevo_eh" rows="3"
                            placeholder="Ingrese las URL's"></textarea>
                    </div>
                    <div id="mje_urls_proy_nuevo_eh">
                    </div>
                </div>

                <div class="col-sm-3">
                 <div class="mb-3">
                    <span class="badge bg-light fs-10 mb-1 text-dark">Dispositivos</span>
                    <input type="hidden" hidden value="DISPOSITIVOS">
                    <textarea class="form-control" id="dispositivos_proy_nuevo_soc" rows="3"
                        placeholder="Engrese dispositivos"></textarea>
                </div>
                    <div id="mje_dispositivos_proy_nuevo_soc">

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Agentes</span>
                        <input type="hidden" hidden value="AGENTES">
                        <textarea class="form-control" id="agentes_proy_nuevo_soc" rows="3"
                            placeholder="Ingrese los agentes"></textarea>
                    </div>
                    <div id="mje_agentes_proy_nuevo_soc">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="mb-3">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Equipos</span>
                        <input type="hidden" hidden value="EQUIPOS">
                        <textarea class="form-control" id="equipos_proy_nuevo_sase" rows="3"
                            placeholder="Ingrese los equipos"></textarea>
                    </div>
                    <div id="mje_equipos_proy_nuevo_sase">
                    </div>
                </div>
                <div class="col-sm-4 ml-1">
                    <div class="mb-3">
                        <input type="hidden" hidden value="OTROS">
                        <span class="badge bg-light fs-10 mb-1 text-dark">Otros activos</span>

                        <textarea class="form-control" id="otros_proy_nuevo" rows="3"
                            placeholder="Otros"></textarea>
                    </div>
                    <div id="mje_urls_proy_nuevo_otros">
                    </div>
                </div>`;
            activarValidacionTextarea(
                "ips_proy_nuevo_eh",
                "mje_ips_proy_nuevo_eh",
                "IP"
            );

            activarValidacionTextarea(
                "urls_proy_nuevo_eh",
                "mje_urls_proy_nuevo_eh",
                "URL"
            );
            break;

        default:
            break;
    }
});

function validarIP(ip) {
    const regexIP = /^(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})){3}$/;
    return regexIP.test(ip);
}

function validarURL(url) {
    return url.startsWith("http://") || url.startsWith("https://");
}

function activarValidacionTextarea(textareaId, mensajeId, tipo) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) return;
    textarea.addEventListener("input", function () {
        if (this.value.trim() === "") {
            document.getElementById(mensajeId).innerHTML = "";
            return;
        }
        const items = this.value
            .split(/[\s,]+/)
            .map(x => x.trim())
            .filter(x => x.length > 0);
        this.value = items.join('\n');
        let invalidos = [];
        if (tipo === "IP") {
            invalidos = items.filter(i => !validarIP(i));
        }
        if (tipo === "URL") {
            invalidos = items.filter(i => !validarURL(i));
        }
        if (invalidos.length > 0) {
            const lista = invalidos.map(i => `<li>${i}</li>`).join('');
            document.getElementById(mensajeId).innerHTML =
                `<div class="alert alert-warning text-center">
                <strong>Formato inválido</strong>
                <ul class="mb-0">${lista}</ul>
            </div>`;
        } else {
            document.getElementById(mensajeId).innerHTML = "";
        }
    });
}

function validar_combo_prioridad(valorInicial) {
    const $combo = $("#combo_prioridad_proy_nuevo");

    const aplicarColor = (valor) => {
        valor = valor.toString();
        $combo.removeClass("border border-success border-warning border-danger");
        switch (valor) {
            case '1':
                $combo.addClass("border border-success");
                break;
            case '2':
                $combo.addClass("border border-warning");
                break;
            case '3':
                $combo.addClass("border border-danger");
                break;
            default:
                $combo.addClass("border border-success");
        }
    };
    aplicarColor(valorInicial);
    $combo.off("change").on("change", function () {
        aplicarColor(this.value);
    });
}

function gestionar_proy_borrador(proy_id, id_proyecto_cantidad_servicios, id) {


    function actualizarTitulo() {

        if (TITULO_EDITADO_MANUAL) return;

        const client_rs = $("#client_rs_alta_proy").val() || "";
        const ref = $("#client_refPro_proy_nuevo").val()?.trim() || "";
        const recurrencia = $("#combo_recurrente_proy_nuevo").val()?.trim().toUpperCase() || "";

        let base = $("#titulo_client_rs_alta_proy").data("base") || client_rs;

        let extra = "";
        let tituloActual = $("#titulo_client_rs_alta_proy").val();

        if (tituloActual.includes(" - ")) {
            extra = " - " + tituloActual.split(" - ")[1];
        }

        let nuevoTitulo = base;

        if (ref) nuevoTitulo += `_Ref ${ref}`;
        if (recurrencia && recurrencia !== "NO" && recurrencia !== "0") {
            nuevoTitulo += `_Recurrente SI`;
        }

        nuevoTitulo += extra;

        $("#titulo_client_rs_alta_proy").val(nuevoTitulo);
    }

    $.ajax({
        type: "POST",
        url: "../../../../../Controller/ctrProyectos.php?proy=get_sector_x_proy",
        data: {
            id: id
        },
        dataType: "json",
        success: function (response) {

            let sector_id = parseInt(response.sector_id);

            console.log("sector:", sector_id);

            if (sector_id === 4) {
                $("#icon_activos").hide();
                $("#span_activos").hide();
            } else {
                $("#icon_activos").show();
                $("#span_activos").show();
            }

        }
    });

    if (!id) {
        $("#cont_activos_ips_urls_otros").show();
        $("#cont_archivo").show();
        $("#cont_activos").hide();
        $("#recurrencia").show();
        $("#cont_combo_workshop").hide();
    } else {
        $("#cont_activos").show();
        $("#recurrencia").hide();
        $("#cont_combo_workshop").show();
        $("#mdl_id_proyecto_gestionado_nuevos_hosts").val(id); //id para modificar hosts de un proeycto creado
    }



    $.post("../../../../../Controller/ctrProyectos.php?proy=get_workshop", {
            id_proyecto_gestionado: $("#id_proyecto_gestionado").val()
        },
        function (data, textStatus, jqXHR) {

            if (data === false || data == null) {
                $("#combo_workshop").val("NO");
            } else {
                if (data.est == 1) {
                    $("#combo_workshop").val("SI");
                } else {
                    $("#combo_workshop").val("NO");
                }
            }
        },
        "json"
    );

    let UPDATE_PROY_RECURRENCIA = false;

    $.post("../../../../../Controller/ctrProyectos.php?proy=validar_si_es_recurrente", {
            id_proyecto_cantidad_servicios: id_proyecto_cantidad_servicios
        },
        function (data, textStatus, jqXHR) {
            if (data.validacion_recurrente == "SI_RECURRENTE") {
                UPDATE_PROY_RECURRENCIA = true;
                $.post("../../../../../Controller/ctrProyectos.php?proy=get_id_proyecto_recurrencia", {
                        id: id
                    },
                    function (data, textStatus, jqXHR) {
                        if (data.id_proyecto_recurrencia != null) {
                            UPDATE_PROY_RECURRENCIA = true; // tiene valor
                        } else {
                            UPDATE_PROY_RECURRENCIA = false; // no tiene valor
                        }

                        if (UPDATE_PROY_RECURRENCIA == false) {
                            $.post("../../../../../Controller/ctrProyectos.php?proy=get_primer_id_proyecto_recurrencia", {
                                    id_proyecto_cantidad_servicios: id_proyecto_cantidad_servicios
                                },
                                function (data, textStatus, jqXHR) {
                                    console.log(data);

                                    const ACTIVO = data.activo;
                                    const ID = data.id;

                                    if (ACTIVO == 'NO') {
                                        $.post("../../../../../Controller/ctrProyectos.php?proy=update_proyecto_recurrencia", {
                                            id: ID,
                                            id_proyecto_gestionado: $("#id_proyecto_gestionado").val()
                                        });
                                        $.post("../../../../../Controller/ctrProyectos.php?proy=update_id_proyecto_recurrencia_proyecto_gestionado", {
                                            id: id,
                                            id_proyecto_recurrencia: ID
                                        });
                                        // $.post("../../../../../Controller/ctrProyectos.php?proy=update_proyecto_recurrencia_posicion_recurrencia", { id: id, id_proyecto_recurrencia: ID });
                                    }
                                },
                                "json"
                            );
                            $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_proyecto_creado", {
                                    id: $("#mdl_id_proyecto_gestionado").val()
                                },
                                function (data, textStatus, jqXHR) {
                                    $("#titulo_client_rs_alta_proy")
                                        .val(data.titulo)
                                        .data("base", data.titulo.split("_Ref")[0]); // CLAVE
                                },
                                "json"
                            );
                            setTimeout(() => {
                                Toastify({
                                    text: "Proyecto agregado a la recurrencia correctamente",
                                    duration: 1000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#0ab39c",
                                }).showToast();
                                if ($.fn.DataTable.isDataTable('#table_proyectos_recurrencia')) {
                                    $("#table_proyectos_borrador").DataTable().ajax.reload(null, false);
                                    $('#table_proyectos_recurrencia').DataTable().ajax.reload(null, false);
                                }
                            }, 300);
                        }
                    },
                    "json"
                );
            } else {
                UPDATE_PROY_RECURRENCIA = false;
                return;
            }
        },
        "json"
    );


    // Esta parte la puedo dejar afuera porque es independiente
    $.post("../../../../../Controller/ctrProyectos.php?proy=get_primer_id_proyecto_gestionado", {
            id_proyecto_cantidad_servicios: id_proyecto_cantidad_servicios
        },
        function (data, textStatus, jqXHR) {
            $("#mdl_id_proyecto_gestionado").val(data.id_proyecto_gestionado);
        },
        "json"
    );

    document.getElementById("form_alta_proyecto").reset();

    $("#mdl_id_proyecto_gestionado").val(id)

    $("#id_proyecto_gestionado").val(id)
    $("#ModalAltaProject").modal("show");
    $("#btn_crear_proyecto").show();
    $("#btn_cambiar_estado_proyecto").hide();
    $("#btn_eliminar_proyecto").show();
    $("#btn_finalizar_estado_proyecto").hide();
    $("#btn_editar_proyecto").hide();
    $("#combo_recurrente_proy_nuevo").show();

    $("#id_proyecto_cantidad_servicios").val(id_proyecto_cantidad_servicios);
    $("#proy_id").val(proy_id);

    function get_data_editar_proyecto() {

        let formData = new FormData();

        let checkboxes = document.querySelectorAll('#combo_usuario_x_sector input[name="usu_asignado[]"]:checked');
        checkboxes.forEach(check => {
            formData.append('usu_asignado[]', check.value);
        });

        let hs_dimensionadas = document.getElementById('hs_dimensionadas').value.trim();


        if (isNaN(hs_dimensionadas) || hs_dimensionadas === "" || parseFloat(hs_dimensionadas) <= 0) {
            Swal.fire({
                icon: "warning",
                title: "Error",
                text: "El campo 'hs_dimensionadas' debe ser un número positivo.",
                timer: 1500
            });
            return null;
        }

        // 🔹 Aseguramos que el ID venga del campo correcto
        let idProyecto = $("#id_proyecto_gestionado").val() || id || null;
        if (!idProyecto) {
            console.error("❌ No se encontró el id_proyecto_gestionado");
            return null;
        }

        formData.append('id', idProyecto);
        formData.append('id_proyecto_gestionado', idProyecto);
        formData.append('cat_id', $("#combo_categoria_proy_nuevo").val());
        formData.append('cats_id', $("#combo_subcategoria_proy_nuevo").val());
        formData.append('sector_id', $("#combo_sector_proy_nuevo").val());
        formData.append('usu_id', $("#combo_usuario_x_sector").val());
        formData.append('prioridad_id', $("#combo_prioridad_proy_nuevo").val());
        formData.append('titulo', $("#titulo_client_rs_alta_proy").val());
        formData.append('descripcion', $("#descripcion_proy").val());
        formData.append('refProy', $("#client_refPro_proy_nuevo").val());
        formData.append('correo_envio_cliente', $("#correo_envio_cliente").val());
        formData.append('correo_envio_cliente_copias', $("#correo_envio_cliente_copias").val());
        formData.append('recurrencia', $("#combo_recurrente_proy_nuevo").val() || 0);
        formData.append('fech_inicio', $("#fech_ini_proy_nuevo").val());
        formData.append('fech_fin', $("#fech_fin_proy_nuevo").val());
        formData.append('fech_vantive', $("#fech_vantive").val());
        formData.append('hs_dimensionadas', hs_dimensionadas);

        return formData;
    }



    $.post("../../../../../Controller/ctrProyectos.php?proy=get_client_y_pais_para_proy_borrador", {
        proy_id: proy_id
    }, function (data) {
        const client_rs = data.client_rs;
        const tituloDefault = `${client_rs}`; // 🔹 Sin fecha de creación

        // Asigna los valores base
        $("#client_rs_alta_proy").val(client_rs);
        $("#pais_id_carga_proy").val(data.pais_nombre);

        $("#titulo_client_rs_alta_proy")
            .val(tituloDefault)
            .data("base", client_rs);

        TITULO_EDITADO_MANUAL = false;

        $("#proy_cliente_periodo").text(data.titulo);

        // Registrar eventos
        $("#client_refPro_proy_nuevo").off("input").on("input", actualizarTitulo);
        $("#combo_recurrente_proy_nuevo").off("change").on("change", actualizarTitulo);
        $("#titulo_client_rs_alta_proy").off("input").on("input", function () {
            TITULO_EDITADO_MANUAL = true;
        });

    }, "json");

    $("#combo_categoria_proy_nuevo").prop("disabled", false);
    $("#combo_subcategoria_proy_nuevo").prop("disabled", false);
    $("#combo_sector_proy_nuevo").prop("disabled", false);
    // $("#client_refPro_proy_nuevo").prop("disabled", false);

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_proyecto_creado", {
        id: $("#mdl_id_proyecto_gestionado").val()
    }, function (data, textStatus, jqXHR) {

        SECTOR_ID = data.sector_id;
        CATEGORIA_ID = data.cat_id;

        $("#contenedor_cont_activos").show();

        if (data.cat_id == 78) {
            $("#cont_combo_workshop").hide();
            $("#contenedor_cont_activos").hide();
        } else {
            $("#cont_combo_workshop").show();
            $("#contenedor_cont_activos").show();
        }

        if (data.recurrencia != '' || data.recurrencia != null) {
            $("#combo_categoria_proy_nuevo").prop("disabled", true);
            // $("#combo_subcategoria_proy_nuevo").prop("disabled", true);
            $("#combo_sector_proy_nuevo").prop("disabled", true);
            // $("#client_refPro_proy_nuevo").prop("disabled", true);
        } else {
            $("#combo_categoria_proy_nuevo").prop("disabled", false);
            $("#combo_subcategoria_proy_nuevo").prop("disabled", false);
            $("#combo_sector_proy_nuevo").prop("disabled", false);
            // $("#client_refPro_proy_nuevo").prop("disabled", false);
        }

        VALIDAR_SI_HAY_FECHA_INICIO = !!data.fech_inicio;

        if (data) {
            $("#combo_recurrente_proy_nuevo").hide();

            $("#cont_activos").show();
            $("#cont_activos_ips_urls_otros").hide();
            $("#btn_crear_proyecto").hide();
            $("#btn_cambiar_estado_proyecto").show();
            $("#btn_eliminar_proyecto").show();
            $("#btn_finalizar_estado_proyecto").show();
            $("#btn_editar_proyecto").show();

            let img = `<img src="${URL + "/View/Home/Public/Uploads/Calidad/" + data.archivo}" width="100%" height="100%" alt="Imagen Proyecto cargado">`;

            $("#descripcion_proy").val(data.descripcion);
            $("#hs_dimensionadas").val(data.hs_dimensionadas);
            $("#client_refPro_proy_nuevo").val(data.refProy);
            $("#correo_envio_cliente").val(data.correo_envio_cliente);
            $("#correo_envio_cliente_copias").val(data.correo_envio_cliente_copias);

            $("#combo_recurrente_proy_nuevo").val(data.recurrencia);
            $("#fech_ini_proy_nuevo").val(data.fech_inicio);

            $("#titulo_client_rs_alta_proy")
                .val(data.titulo)
                .data("base", data.titulo.split("_Ref")[0]); // 🔥 CLAVE

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_sectores", function (res) {
                $("#combo_sector_proy_nuevo").html(res);
                $("#combo_sector_proy_nuevo").val(data.sector_id);

                $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_categorias_x_sector", {
                    sector_id: data.sector_id
                }, function (res) {
                    $("#combo_categoria_proy_nuevo").html(res);
                    $("#combo_categoria_proy_nuevo").val(data.cat_id);
                });

                $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_subcategorias_x_sector", {
                    sector_id: data.sector_id
                }, function (res) {
                    $("#combo_subcategoria_proy_nuevo").html(res);
                    $("#combo_subcategoria_proy_nuevo").val(data.cats_id);
                });



                $.post("../../../../../Controller/ctrProyectos.php?proy=get_usuarios_x_sector", {
                    sector_id: data.sector_id,
                    id_proyecto_gestionado: id
                }, function (res) {
                    $("#combo_usuario_x_sector").html(res);
                });

                $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_prioridad_proy_nuevo_eh", function (res) {
                    $("#combo_prioridad_proy_nuevo").html(res);
                    $("#combo_prioridad_proy_nuevo").val(data.prioridad_id);
                    validar_combo_prioridad(data.prioridad_id);
                });
            });

            $("#fech_fin_proy_nuevo").val(data.fech_fin);
            $("#fech_vantive").val(data.fech_vantive);

            $("#cont_img_proy_cargado").html(img);
            $("#cont_archivo").hide();


            $("#btn_cambiar_estado_proyecto").off().on("click", function (e) {
                e.preventDefault();

                if ($("#fech_ini_proy_nuevo").val() == '' || VALIDAR_SI_HAY_FECHA_INICIO == false) {
                    Swal.fire({
                        icon: "warning",
                        title: "Error",
                        text: "Debe seleccionar una fecha inicio",
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    $.post("../../../../../Controller/ctrProyectos.php?proy=cambiar_a_abierto", {
                            id: $("#id_proyecto_gestionado").val()
                        },
                        function (data, textStatus, jqXHR) {
                            Swal.fire({
                                icon: "success",
                                title: "Bien",
                                text: "Proyecto pasado a nuevo!",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        "json"
                    );

                    $.post(
                        "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                            id_proyecto_gestionado: $("#id_proyecto_gestionado").val(),
                            estados_id: 1
                        }
                    );

                    setTimeout(() => {
                        if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                            $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                        }
                        if ($.fn.DataTable.isDataTable('#table_proyectos_nuevos_eh_vas')) {
                            $('#table_proyectos_nuevos_eh_vas').DataTable().ajax.reload(null, false);
                        }
                        if ($.fn.DataTable.isDataTable('#table_proyectos_en_proceso')) {
                            $('#table_proyectos_en_proceso').DataTable().ajax.reload(null, false);
                        }
                        if ($.fn.DataTable.isDataTable('#table_proyectos_recurrencia')) {
                            $('#table_proyectos_recurrencia').DataTable().ajax.reload(null, false);
                        }
                        if ($.fn.DataTable.isDataTable('#table_proyectos_total_calidad')) {
                            $('#table_proyectos_total_calidad').DataTable().ajax.reload(null, false);
                        }
                    }, 500);

                    Swal.fire({
                        icon: "success",
                        title: "Bien",
                        text: "Proyecto pasado a Nuevo!",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $("#ModalAltaProject").modal("hide");
                }
            });

            // ===============================
            // BOTÓN: Editar proyecto
            // ===============================
            $("#btn_editar_proyecto").attr("type", "button").off().on("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                let sectorActual = $("#combo_sector_proy_nuevo").val();
                let categoriaActual = $("#combo_categoria_proy_nuevo").val();

                if (
                    parseInt(sectorActual) !== parseInt(SECTOR_ID) ||
                    parseInt(categoriaActual) !== parseInt(CATEGORIA_ID)
                ) {
                    Swal.fire({
                        icon: "warning",
                        title: "Error",
                        text: "No es posible modificar el sector o la categoría del proyecto. Actualice la página para restaurar los valores originales y continuar"
                    });
                    return;
                }


                let dataForm = get_data_editar_proyecto();

                if (!dataForm) return; // Detiene si hubo error en la validación
                $.ajax({
                    type: "POST",
                    url: "../../../../../Controller/ctrProyectos.php?proy=update_proyecto",
                    data: dataForm,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        console.log("proyecto completo actualizado", response);

                        if (response.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "¡Bien!",
                                text: "Proyecto actualizado correctamente",
                                showCancelButton: false,
                                showConfirmButton: false,
                                timer: 1500
                            });

                            $.post(
                                "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                                    id_proyecto_gestionado: dataForm.get('id_proyecto_gestionado'),
                                    estados_id: 18
                                }
                            );

                            setTimeout(() => {
                                VALIDAR_SI_HAY_FECHA_INICIO = true;
                                if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                                    $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                                }
                            }, 500);
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.message || "Hubo un error al actualizar el proyecto",
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function (xhr) {
                        console.error("error al actualizar proyecto", xhr.responseText);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Datos invalidos",
                            showConfirmButton: true
                        });
                    }
                });


                $.post("../../../../../Controller/ctrProyectos.php?proy=get_workshop", {
                        id_proyecto_gestionado: $("#id_proyecto_gestionado").val()
                    },
                    function (data, textStatus, jqXHR) {

                        if (data == false) {
                            if ($("#combo_workshop").val() == "SI") {
                                $.ajax({
                                    type: "POST",
                                    url: "../../../../../Controller/ctrProyectos.php?proy=insert_workshop",
                                    data: {
                                        id_proyecto_gestionado: $("#id_proyecto_gestionado").val()
                                    },
                                    dataType: "json",
                                    success: function (response) {},
                                    error: function (err) {
                                        console.log(err);
                                    }
                                });
                            }
                        } else {
                            if (data.est == 0 && $("#combo_workshop").val() == "SI") {
                                $.ajax({
                                    type: "POST",
                                    url: "../../../../../Controller/ctrProyectos.php?proy=update_workshop",
                                    data: {
                                        id_proyecto_gestionado: $("#id_proyecto_gestionado").val(),
                                        est: 1
                                    },
                                    dataType: "json",
                                    success: function (response) {},
                                    error: function (err) {
                                        console.log(err);
                                    }
                                });
                            } else if (data.est == 1 && $("#combo_workshop").val() == "NO") {
                                $.ajax({
                                    type: "POST",
                                    url: "../../../../../Controller/ctrProyectos.php?proy=update_workshop",
                                    data: {
                                        id_proyecto_gestionado: $("#id_proyecto_gestionado").val(),
                                        est: 0
                                    },
                                    dataType: "json",
                                    success: function (response) {},
                                    error: function (err) {
                                        console.log(err);

                                    }
                                });
                            }
                        }
                    },
                    "json"
                );
            });

        } else {
            validar_combo_prioridad(1);
            $("#cont_activos").hide();
            $("#cont_activos_ips_urls_otros").show();
            $("#cont_archivo").show();
            $("#cont_descripcion_proy").show();
        }
    }, "json");

    const elRefPro = document.getElementById("client_refPro_proy_nuevo");
    if (elRefPro) elRefPro.focus();
    $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_categorias_x_sector", {
            sector_id: 1
        },
        function (data, textStatus, jqXHR) {
            $("#combo_categoria_proy_nuevo").html(data)
        },
        "html"
    );

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_subcategorias_x_sector", {
            sector_id: 1
        },
        function (data, textStatus, jqXHR) {
            $("#combo_subcategoria_proy_nuevo").html(data)
        },
        "html"
    );
    $("#contenedor_validad_proy_Desa_interno_tasking").hide();



    $("#combo_categoria_proy_nuevo").change(function (e) {
        e.preventDefault();
        $("#btn_crear_proyecto").show();
        $("#btn_eliminar_proyecto").show();
        if (this.value == 78) {
            $("#combo_recurrente_proy_nuevo").val(0)
            $.post(
                "../../../../../Controller/ctrProyectos.php?proy=get_datos_proyectos_tasking",
                function (data) {

                    console.log(data);

                    // si no hay datos o viene false desde backend
                    if (!data || data === false) {
                        $("#btn_crear_proyecto").show();
                        $("#btn_eliminar_proyecto").show();
                        return;
                    }

                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);

                    const fechaHabilitacion = new Date(
                        data.fech_habilitacion + "T00:00:00"
                    );

                    // VALIDACIÓN REAL
                    if (hoy >= fechaHabilitacion && data.finalizado === "SI") {
                        $("#btn_crear_proyecto").show();
                        $("#btn_eliminar_proyecto").show();
                    } else {
                        $("#btn_crear_proyecto").hide();
                        $("#btn_eliminar_proyecto").hide();
                        Swal.fire({
                            icon: "warning",
                            title: "Error",
                            text: "No es posible cargar un nuevo proyecto de Desarrollo Tasking hasta que el anterior haya finalizado y se haya cumplido el plazo de dos meses",
                            showConfirmButton: true
                        });
                    }
                },
                "json"
            );
        }

        if (this.value == 78) { //Es un proyecto para Tasking
            $("#cont_activos_ips_urls_otros").hide();
            $("#contenedor_validad_proy_Desa_interno_tasking").show();
            $("#recurrencia").hide();
            $("#combo_recurrente_proy_nuevo").hide();
        } else {
            $("#cont_activos_ips_urls_otros").show();
            $("#contenedor_validad_proy_Desa_interno_tasking").hide();
            $("#recurrencia").show();
            $("#combo_recurrente_proy_nuevo").show();
        }
    });

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_prioridad_proy_nuevo_eh",
        function (data, textStatus, jqXHR) {
            $("#combo_prioridad_proy_nuevo").html(data)
        },
        "html"
    );

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_sectores",
        function (data, textStatus, jqXHR) {
            $("#combo_sector_proy_nuevo").html(data)
        },
        "html"
    );

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_usuarios_x_sector", {
            sector_id: 1
        },
        function (data, textStatus, jqXHR) {
            $("#combo_usuario_x_sector").html(data)
        },
        "html"
    );

    const elComboSector = document.getElementById("combo_sector_proy_nuevo");
    if (elComboSector) {
        elComboSector.addEventListener("change", function () {
            document.getElementById('usuarios_sector').checked = false;
            $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_categorias_x_sector", {
                    sector_id: this.value
                },
                function (data, textStatus, jqXHR) {
                    $("#combo_categoria_proy_nuevo").html(data)
                },
                "html"
            );
            $.post("../../../../../Controller/ctrProyectos.php?proy=get_combo_subcategorias_x_sector", {
                    sector_id: this.value
                },
                function (data, textStatus, jqXHR) {
                    $("#combo_subcategoria_proy_nuevo").html(data)
                },
                "html"
            );
            $.post("../../../../../Controller/ctrProyectos.php?proy=get_usuarios_x_sector", {
                    sector_id: this.value
                },
                function (data, textStatus, jqXHR) {
                    $("#combo_usuario_x_sector").html(data)
                },
                "html"
            );
        });
    }

    function validarHost(tipo, host) {
        host = host.trim();
        if (tipo === 'IP') {
            // Regex IP v4 estricta
            const ipRegex = /^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}$/;
            return ipRegex.test(host);
        }
        if (tipo === 'URL') {
            // Debe empezar con http:// o https://
            const urlRegex = /^https?:\/\/.+/i;
            return urlRegex.test(host);
        }
        // OTRO
        return true;
    }

    function data_hosts_nuevos() {
        let formData = new FormData();
        const tipo = document.getElementById('combo_select_activo').value;
        const hostsRaw = document.getElementById('agregar_nuevo_host').value;

        const hosts = hostsRaw
            .split('\n')
            .map(h => h.trim())
            .filter(h => h !== '');

        if (hosts.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "Debe ingresar al menos un activo"
            });
            return null;
        }
        const invalidos = hosts.filter(h => !validarHost(tipo, h));
        if (invalidos.length > 0) {
            Swal.fire({
                icon: "error",
                title: "Formato inválido",
                html: `Los siguientes valores no son válidos:<br><b>${invalidos.join('<br>')}</b>`
            });
            return null;
        }
        formData.append('id_proyecto_gestionado', id);
        formData.append('id_proyecto_cantidad_servicios', id_proyecto_cantidad_servicios);
        formData.append('tipo', tipo);
        hosts.forEach(h => formData.append('hosts[]', h));
        return formData;
    }

    function ajax_insert_host_nuevos(data) {
        $.ajax({
            type: "POST",
            url: "../../../../../Controller/ctrProyectos.php?proy=insert_nuevos_host",
            data: data,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function () {
                Swal.fire({
                    icon: "success",
                    title: "Activos agregados correctamente",
                    showConfirmButton: false,
                    timer: 1500
                });
                if ($.fn.DataTable.isDataTable('#table_container_activos_proy_creado')) {
                    $('#table_container_activos_proy_creado').DataTable().ajax.reload(null, false);
                }
                $("#ModalAgregarActivos").modal("hide");
            }
        });
    }

    $("#btn_agregar_nuevos_hosts_borrador").off().on("click", function () {
        let data = data_hosts_nuevos();
        if (!data) return;

        ajax_insert_host_nuevos(data);
    });


    //quede acá
    function get_datos_insert_proyecto_gestionado() {
        let formData = new FormData();

        let archivoInput = document.getElementById('archivo');
        if (archivoInput.files.length > 0) {
            formData.append('archivo', archivoInput.files[0]);
        }


        let checkboxes = document.querySelectorAll('#combo_usuario_x_sector input[name="usu_asignado[]"]:checked');
        checkboxes.forEach((check, index) => {
            formData.append('usu_asignado[]', check.value);
        })

        formData.append('id_proyecto_cantidad_servicios', id_proyecto_cantidad_servicios);
        formData.append('cat_id', document.getElementById('combo_categoria_proy_nuevo').value);
        formData.append('cats_id', document.getElementById('combo_subcategoria_proy_nuevo').value);
        formData.append('usu_id', document.getElementById('combo_usuario_x_sector').value);
        formData.append('sector_id', document.getElementById('combo_sector_proy_nuevo').value);
        formData.append('prioridad_id', document.getElementById('combo_prioridad_proy_nuevo').value);
        formData.append('titulo', document.getElementById('titulo_client_rs_alta_proy').value);
        formData.append('descripcion', document.getElementById('descripcion_proy').value);
        formData.append('refProy', document.getElementById('client_refPro_proy_nuevo').value);
        formData.append('correo_envio_cliente', document.getElementById('correo_envio_cliente').value);
        formData.append('correo_envio_cliente_copias', document.getElementById('correo_envio_cliente_copias').value);

        formData.append('recurrencia', document.getElementById('combo_recurrente_proy_nuevo').value);
        formData.append('fech_inicio', document.getElementById('fech_ini_proy_nuevo').value);
        formData.append('fech_fin', document.getElementById('fech_fin_proy_nuevo').value);
        formData.append('fech_vantive', document.getElementById('fech_vantive').value);
        formData.append('captura_imagen', document.getElementById('captura_imagen').value);

        formData.append('ips', document.getElementById("ips_proy_nuevo_eh")?.value || "");
        formData.append('urls', document.getElementById("urls_proy_nuevo_eh")?.value || "");
        formData.append('otros', document.getElementById("otros_proy_nuevo")?.value || "");
        formData.append('equipos', document.getElementById("equipos_proy_nuevo_sase")?.value || "");
        formData.append('agentes', document.getElementById("agentes_proy_nuevo_soc")?.value || "");
        formData.append('dispositivos', document.getElementById("dispositivos_proy_nuevo_soc")?.value || "");

        formData.append('hs_dimensionadas', document.getElementById('hs_dimensionadas').value);
        return formData;
    }

    function ajax_insert_proyecto_gestionado(data) {
        $.ajax({
            type: "POST",
            url: "../../../../../Controller/ctrProyectos.php?proy=insert_proyecto_gestionado",
            data: data,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                $("#cont_mje_proy_archivo").html("").hide();
                Swal.fire({
                    icon: "success",
                    title: "Bien",
                    text: "Proyecto creado con exito",
                    showConfirmButton: false,
                    timer: 1300
                });

                $("#ModalAltaProject").modal("hide");
                $("#btn_crear_proyecto").hide();
                $("#btn_cambiar_estado_proyecto").show();
                $("#btn_eliminar_proyecto").show();
                $("#btn_finalizar_estado_proyecto").show();
                $("#btn_editar_proyecto").show();

                setTimeout(() => {
                    if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                        $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                        $('#table_proyectos_recurrencia').DataTable().ajax.reload(null, false);
                        $('#table_proyectos_total_calidad').DataTable().ajax.reload(null, false);
                        $('#tablelHistorialProyectosCalidad').DataTable().ajax.reload(null, false);
                    }
                }, 500);

                setTimeout(() => {
                    $('#table_cross_sell_sectores').DataTable().ajax.reload(null, false);
                }, 500)

            },
            error: function (error) {
                let htmlmje = `<div id="extension_no_permitida" class="alert alert-warning text-center" role="alert">
                    <a class="alert-link">Error! <br></a>Extension no permitida
                </div>`;
                $("#cont_mje_proy_archivo").html(htmlmje).show();
                $("#archivo").val("");
                setTimeout(() => {
                    $("#cont_mje_proy_archivo").fadeOut();
                }, 1500);
            }

        });
    }

    function captura_imagen_b64() {
        document.getElementById("captura_imagen").addEventListener("paste", function (e) {
            let clipboardData = (e.clipboardData || window.clipboardData);
            // Buscar si hay items tipo imagen
            let items = clipboardData.items;
            let foundImage = false;

            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") !== -1) {
                    let file = items[i].getAsFile();
                    let reader = new FileReader();
                    reader.onload = function (event) {
                        // Insertar base64 en el input
                        document.getElementById("captura_imagen").value = event.target.result;
                    };
                    reader.readAsDataURL(file);
                    foundImage = true;
                    break;
                }
            }
            if (!foundImage) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: "Error!",
                    text: "Solo se permiten imágenes en formato base64",
                    showConfirmButton: false,
                    showCancelButton: false,
                    timer: 1100
                });
            }
        });
    }
    captura_imagen_b64();

    $("#btn_crear_proyecto").off().click(function (e) {
        e.preventDefault();

        let data = get_datos_insert_proyecto_gestionado();
        let hs_dimensionadas = data.get('hs_dimensionadas').trim();

        let validarHsDimRequerido = false;

        // Validar campo vacío
        if (hs_dimensionadas === '') {
            Swal.fire({
                icon: "warning",
                title: "Error!",
                text: "Error en el campo Dimensionamiento",
                showConfirmButton: true,
                showCancelButton: false
            });
            return;
        }

        // Validar que sea un número entero positivo (sin decimales, sin signos, sin letras)
        if (!/^[1-9]\d*$/.test(hs_dimensionadas)) {
            Swal.fire({
                icon: "warning",
                title: "Error!",
                text: "Error en el campo Dimensionamiento",
                showConfirmButton: true,
                showCancelButton: false
            });
            return;
        } else if (data.get('fech_inicio') == '' || data.get('fech_inicio') == null) {
            Swal.fire({
                icon: "warning",
                title: "Error!",
                text: "Debe seleccionar una fecha de inicio de proyecto",
                showConfirmButton: true,
                showCancelButton: false
            });
        } else {
            ajax_insert_proyecto_gestionado(data);

        }
    });

    //Comienza validacion de IPS en TEXTAREA
    const elIps = document.getElementById("ips_proy_nuevo_eh");
    if (elIps) {
        elIps.addEventListener("input", function () {
            const textarea = this;
            if (textarea.value.trim() === "") {
                document.getElementById("mje_ips_proy_nuevo_eh").innerHTML = "";
                return;
            }
            const todasLasIps = textarea.value
                .split(/[\s,]+/)
                .map(ip => ip.trim())
                .filter(ip => ip.length > 0);
            textarea.value = todasLasIps.join('\n');
            const ipsInvalidas = todasLasIps.filter(ip => !validarIP(ip));
            if (ipsInvalidas.length > 0) {
                mostrarMensajeIpInvalida(ipsInvalidas);
            } else {
                eliminarMensajeIpInvalida();
            }
        });
    }

    function validarIP(ip) {
        const regexIP = /^(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})){3}$/;
        return regexIP.test(ip);
    }

    function mostrarMensajeIpInvalida(invalidas) {
        const contenedor = document.getElementById("mje_ips_proy_nuevo_eh");
        const lista = invalidas.map(ip => `<li>${ip}</li>`).join('');
        contenedor.innerHTML = `
        <div id="mje_validar_ips" class="alert alert-warning text-center" role="alert">
            <strong>¡Error!</strong> Las siguientes IPs no son válidas:
            <ul class="mb-0">${lista}</ul>
        </div>`;
    }

    function eliminarMensajeIpInvalida() {
        document.getElementById("mje_ips_proy_nuevo_eh").innerHTML = "";
    }
    //Finaliza validacion de IPS en TEXTAREA

    //Comienza validacion URLS en TEXTAREA
    const elUrls = document.getElementById("urls_proy_nuevo_eh");
    if (elUrls) {
        elUrls.addEventListener("input", function () {
            const textarea = this;
            if (textarea.value.trim() === "") {
                document.getElementById("mje_urls_proy_nuevo_eh").innerHTML = "";
                return;
            }
            const todasLasUrls = textarea.value
                .split(/[\s,]+/)
                .map(url => url.trim())
                .filter(url => url.length > 0);
            textarea.value = todasLasUrls.join('\n');
            const urlsInvalidas = todasLasUrls.filter(url => !validarURL(url));
            if (urlsInvalidas.length > 0) {
                mostrarMensajeUrlInvalida(urlsInvalidas);
            } else {
                eliminarMensajeUrlInvalida();
            }
        });
    } // cierra el if (elUrls)

    function validarURL(url) {
        return url.startsWith("http://") || url.startsWith("https://");
    }

    function mostrarMensajeUrlInvalida(invalidas) {
        const contenedor = document.getElementById("mje_urls_proy_nuevo_eh");
        const lista = invalidas.map(url => `<li>${url}</li>`).join('');
        contenedor.innerHTML = `
        <div id="mje_validar_urls" class="alert alert-warning text-center" role="alert">
            <strong>¡Error!</strong> Las siguientes URLs no comienzan con <code>http://</code> o <code>https://</code>:
            <ul class="mb-0">${lista}</ul>
        </div>`;
    }

    function eliminarMensajeUrlInvalida() {
        document.getElementById("mje_urls_proy_nuevo_eh").innerHTML = "";
    }

} // cierra gestionar_proy_borrador


function consultar_activos_borrdor(proy_id, numero_proyecto) {
    $("#ModalConsultarActivos").modal("show")
    tabla = $("#table_container_activos_proy_creado").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "paging": false, // 👈 Esto elimina la paginación
        "searching": true,
        "lengthChange": false,
        "colReorder": true,
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: "../../../../../Controller/ctrProyectos.php?proy=get_host_proy_borrador",
            type: "post",
            dataType: "json",
            data: {
                id_proyecto_gestionado: $("#id_proyecto_gestionado").val()
            },
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "order": [
            [0, "desc"]
        ],
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando..",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados..",
            "sEmptyTable": "Ninguna tarea disponible en esta tabla",
            "sInfo": "",
            "sInfoEmpty": "",
            "sInfoFiltered": "(Filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar: ",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ":Activar para ordenar la columna de manera ascendiente",
                "sSortDescending": ":Activar para ordenar la columna de manera descendiente"
            }
        }
    });
}



function actualizarComboActivos(valor) {
    valor = valor.toString();

    if (valor === '4') {
        $("#combo_select_activo").html(""); // limpia el select
        return;
    }

    let opciones = "";

    switch (valor) {

        case '1':
            opciones = `
                <option value="IP">IP's</option>
                <option value="URL">URL's</option>
                <option value="OTRO">Otros</option>
            `;
            break;
        case '2':
            opciones = `
                <option value="DISPOSITIVO">Dispositivos</option>
                <option value="AGENTE">Agentes</option>
                <option value="OTRO">Otros</option>
            `;
            break;

        case '3':
            opciones = `
                <option value="IP">IP's</option>
                <option value="EQUIPO">Equipos</option>
                <option value="OTRO">Otros</option>
            `;
            break;
        case '5':
            opciones = `
                <option value="IP">IP's</option>
                <option value="URL">URL's</option>
                <option value="DISPOSITIVO">Dispositivos</option>
                <option value="AGENTE">Agentes</option>
                <option value="EQUIPO">Equipos</option>
                <option value="OTRO">Otros</option>
            `;
            break;
    }

    $("#combo_select_activo").html(opciones);
}

$(document).ready(function () {

    $("#mdl_id_proyecto_gestionado_nuevos_hosts").on("change", function () {
        actualizarComboActivos($(this).val());
    });

});

function agregar_activos_borrdor() {

    $("#agregar_activos_borrador")[0].reset();

    let idProyecto = $("#mdl_id_proyecto_gestionado_nuevos_hosts").val();

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_sector_x_proy", {
        id: idProyecto
    }, function (data) {

        // Si el sector es 4, no mostrar modal
        if (data.sector_id == 4) {
            return;
        }

        actualizarComboActivos(data.sector_id);

        $("#ModalAgregarActivos").modal("show");

    }, "json");
}

function gestionar_numero_servicio(cantidad_servicios, proy_id) {
    $("#" + proy_id).text(cantidad_servicios);
    $("#valor_cantidad_servicios").val(cantidad_servicios);
}


function validarIP(ip) {
    const regexIP = /^(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})){3}$/;
    return regexIP.test(ip);
}

function validarURL(url) {
    return url.startsWith("http://") || url.startsWith("https://");
}

function mostrarMensajeInvalido(lista, tipo) {
    const contenedor = document.getElementById("mje_host_agregar");
    const items = lista.map(item => `<li>${item}</li>`).join('');
    const mensaje = tipo === "IP" ?
        `<strong>¡Error!</strong> Las siguientes IPs no son válidas:` :
        `<strong>¡Error!</strong> Las siguientes URLs no comienzan con <code>http://</code> o <code>https://</code>:`;

    contenedor.innerHTML = `
        <div class="alert alert-warning text-center" role="alert">
            ${mensaje}
            <ul class="mb-0">${items}</ul>
        </div>`;
}

function eliminarMensajeInvalido() {
    document.getElementById("mje_host_agregar").innerHTML = "";
}

// ✅ Evento de validación dinámica según opción seleccionada
const inputHost = document.getElementById("agregar_nuevo_host");

if (inputHost) {
    inputHost.addEventListener("input", function () {

        const tipo = document.getElementById("combo_select_activo").value;
        const texto = this.value.trim();

        if (texto === "") {
            eliminarMensajeInvalido();
            return;
        }

        const items = texto
            .split(/[\s,]+/)
            .map(t => t.trim())
            .filter(t => t.length > 0);

        this.value = items.join('\n');

        if (tipo === "IP") {
            const invalidas = items.filter(ip => !validarIP(ip));
            if (invalidas.length > 0) mostrarMensajeInvalido(invalidas, "IP");
            else eliminarMensajeInvalido();
        } else if (tipo === "URL") {
            const invalidas = items.filter(url => !validarURL(url));
            if (invalidas.length > 0) mostrarMensajeInvalido(invalidas, "URL");
            else eliminarMensajeInvalido();
        } else {
            eliminarMensajeInvalido();
        }
    });
}

// Limpiar mensajes al cambiar de tipo
const comboActivo = document.getElementById("combo_select_activo");

if (comboActivo) {
    comboActivo.addEventListener("change", function () {
        eliminarMensajeInvalido();
        document.getElementById("agregar_nuevo_host").value = "";
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

function cambiar_a_borrador(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea pasar el proyecto a Borrador?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                    id: id_proyecto_gestionado,
                    estados_id: 14
                },
                function (data, textStatus, jqXHR) {

                },
                "json"
            );

            $.post(
                "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                    id_proyecto_gestionado: id_proyecto_gestionado,
                    estados_id: 14
                }
            );

            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                    $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                }
            }, 500);
            // $('#table_proyectos_nuevos_eh_pentest').DataTable().ajax.reload(null, false);
            // $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
            // $('#table_proyectos_abiertos_eh_wireless').DataTable().ajax.reload(null, false);
            // $('#table_proyectos_nuevos_eh_wireless').DataTable().ajax.reload(null, false);
            // $('#table_proyectos_en_proceso').DataTable().ajax.reload(null, false);
            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Nuevo!",
                timer: 1500,
                showConfirmButton: false
            });
        }
    })
}


function inactivar_host_borrador(id_proyecto_gestionado, host_id) {
    Swal.fire({
        title: '¿Estás seguro de dar de baja este host?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
    }).then((resul) => {
        if (resul.isConfirmed) {
            $.post("../../../../../Controller/ctrProyectos.php?proy=inactivar_host_x_id", {
                    id_proyecto_gestionado: id_proyecto_gestionado,
                    host_id: host_id
                },
                function (data, textStatus, jqXHR) {
                    Swal.fire({
                        title: 'Host dado de baja con éxito',
                        icon: 'success',
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 1300
                    });
                },
                "json"
            );

            Swal.fire({
                title: 'Host eliminado',
                icon: 'success',
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1000
            });
            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#table_container_activos_proy_creado')) {
                    $('#table_container_activos_proy_creado').DataTable().ajax.reload(null, false);
                }
            }, 500);
        }
    });
}

function activar_host_borrador(id_proyecto_cantidad_servicios, host_id) {
    Swal.fire({
        title: '¿Activar host?',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
    }).then((resul) => {
        if (resul.isConfirmed) {
            $.post("../../../../../Controller/ctrProyectos.php?proy=activar_host_x_id", {
                    id_proyecto_cantidad_servicios: id_proyecto_cantidad_servicios,
                    host_id: host_id
                },
                function (data, textStatus, jqXHR) {
                    Swal.fire({
                        title: 'Host activado con exito',
                        icon: 'success',
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 1300
                    });
                },
                "json"
            );
            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#table_container_activos_proy_creado')) {
                    $('#table_container_activos_proy_creado').DataTable().ajax.reload(null, false);
                }
            }, 500);
        }
    })
}
//***************  Borradores  *****************************
function cambiar_proy_a_borrador(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea pasar el proyecto a Borrador?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                    id: id_proyecto_gestionado,
                    estados_id: 14
                },
                function (data, textStatus, jqXHR) {

                },
                "json"
            );
            $.post(
                "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                    id_proyecto_gestionado: id_proyecto_gestionado,
                    estados_id: 14
                }
            );
            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Borrador!",
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                    $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                    $('#table_proyectos_realizados').DataTable().ajax.reload(null, false);
                    $('#table_proyectos_total').DataTable().ajax.reload(null, false);
                    $('#tablelHistorialProyectosCalidad').DataTable().ajax.reload(null, false);
                }

            }, 500);

        }
    })
}

function cambiar_proy_a_nuevo(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea pasar el proyecto a Nuevo?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                    id: id_proyecto_gestionado,
                    estados_id: 1
                },
                function (data, textStatus, jqXHR) {

                },
                "json"
            );
            $.post(
                "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                    id_proyecto_gestionado: id_proyecto_gestionado,
                    estados_id: 1
                }
            );
            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Nuevo!",
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {

                if ($.fn.DataTable.isDataTable('#table_proyectos_en_proceso')) {
                    $('#table_proyectos_en_proceso').DataTable().ajax.reload(null, false);
                }
                if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                    $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                    $('#table_proyectos_realizados').DataTable().ajax.reload(null, false);
                    $('#table_proyectos_total').DataTable().ajax.reload(null, false);
                    $('#tablelHistorialProyectosCalidad').DataTable().ajax.reload(null, false);
                }

            }, 500);

        }
    })
}

function cerrar_proyecto(id_proyecto_gestionado) {
    Swal.fire({
        icon: "info",
        title: "Desea Cerrar el proyecto?",
        showConfirmButton: true,
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../../../../Controller/ctrProyectos.php?proy=update_estado_proy", {
                    id: id_proyecto_gestionado,
                    estados_id: 4
                },
                function (data, textStatus, jqXHR) {

                },
                "json"
            );

            $.post(
                "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                    id_proyecto_gestionado: id_proyecto_gestionado,
                    estados_id: 4
                }
            );

            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#table_proyectos_realizados')) {
                    $('#table_proyectos_realizados').DataTable().ajax.reload(null, false);
                    $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                }
            }, 500);

            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Proyecto pasado a Nuevo!",
                timer: 1500,
                showConfirmButton: false
            });
        }
    })
}

const btnEliminar = document.getElementById("btn_eliminar_proyecto");
if (btnEliminar) {
    btnEliminar.addEventListener("click", (e) => {
        const ID_PROYECTO_GESTIONADO = document.getElementById("mdl_id_proyecto_gestionado").value;

        const ID_PROYECTO_CANTIDAD_SERVICIOS = document.getElementById("id_proyecto_cantidad_servicios").value;
        e.preventDefault();
        if (!ID_PROYECTO_GESTIONADO) {
            Swal.fire({
                icon: "warning",
                title: "Atencion",
                text: "Desea eliminar este proyecto?",
                showCancelButton: true,
                showConfirmButton: true
            }).then((resutl) => {
                if (resutl.isConfirmed) {
                    $.post("../../../../../Controller/ctrProyectos.php?proy=cambiar_estado_proyecto_cantidad_servicios", {
                            id_proyecto_cantidad_servicios: ID_PROYECTO_CANTIDAD_SERVICIOS
                        },
                        function (data, textStatus, jqXHR) {

                        },
                        "json"
                    );

                    Swal.fire({
                        icon: "success",
                        title: "Bien",
                        text: "Proyecto eliminado correctamente",
                        timer: 1100,
                        showCancelButton: false,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        $("#ModalAltaProject").modal("hide");
                        if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                            $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                            $('#table_proyectos_total_calidad').DataTable().ajax.reload(null, false);
                        }
                    }, 500);
                }
            })
        } else {
            Swal.fire({
                icon: "warning",
                title: "Atencion",
                text: "Desea eliminar este proyecto?",
                showCancelButton: true,
                showConfirmButton: true
            }).then((resutl) => {
                if (resutl.isConfirmed) {

                    $.post("../../../../../Controller/ctrProyectos.php?proy=inhabilitar_proyectos_DesarrolloTasking", {
                            id_proyecto_gestionado: ID_PROYECTO_GESTIONADO
                        },
                        function (data, textStatus, jqXHR) {

                        },
                        "json"
                    );

                    $.post("../../../../../Controller/ctrProyectos.php?proy=cambiar_a_eliminado_proyecto_gestionado", {
                            id: ID_PROYECTO_GESTIONADO,
                            estados_id: 16
                        },
                        function (data, textStatus, jqXHR) {

                        },
                        "json"
                    );

                    $.post(
                        "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                            id_proyecto_gestionado: ID_PROYECTO_GESTIONADO,
                            estados_id: 16
                        }
                    );

                    Swal.fire({
                        icon: "success",
                        title: "Bien",
                        text: "Proyecto eliminado correctamente",
                        timer: 1100,
                        showCancelButton: false,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        $("#ModalAltaProject").modal("hide");
                        if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                            $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                        }
                    }, 500);
                }
            })
        }
    })
}

const btnFinalizar = document.getElementById("btn_finalizar_estado_proyecto");
if (btnFinalizar) {
    btnFinalizar.addEventListener("click", (e) => {
        const ID_PROYECTO_GESTIONADO = document.getElementById("mdl_id_proyecto_gestionado").value;
        const ID_PROYECTO_CANTIDAD_SERVICIOS = document.getElementById("id_proyecto_cantidad_servicios").value;
        e.preventDefault();
        Swal.fire({
            icon: "warning",
            title: "Atencion",
            text: "Desea finalizar sin implementar este proyecto?",
            showCancelButton: true,
            showConfirmButton: true
        }).then((resutl) => {
            if (resutl.isConfirmed) {
                $.post("../../../../../Controller/ctrProyectos.php?proy=cambiar_a_eliminado_proyecto_gestionado", {
                        id: ID_PROYECTO_GESTIONADO,
                        estados_id: 15
                    },
                    function (data, textStatus, jqXHR) {

                    },
                    "json"
                );

                $.post("../../../../../Controller/ctrProyectos.php?proy=inhabilitar_proyectos_DesarrolloTasking", {
                        id_proyecto_gestionado: ID_PROYECTO_GESTIONADO
                    },
                    function (data, textStatus, jqXHR) {

                    },
                    "json"
                );

                $.post(
                    "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                        id_proyecto_gestionado: ID_PROYECTO_GESTIONADO,
                        estados_id: 15
                    }
                );

                Swal.fire({
                    icon: "success",
                    title: "Bien",
                    text: "Proyecto eliminado correctamente",
                    timer: 1100,
                    showCancelButton: false,
                    showConfirmButton: false
                });
                setTimeout(() => {
                    $("#ModalAltaProject").modal("hide");
                    if ($.fn.DataTable.isDataTable('#table_proyectos_borrador')) {
                        $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                    }
                }, 500);
            }
        })
    })
}

function initTablaHistorial(client_id, mostrar) {
    if ($.fn.DataTable.isDataTable('#tablelHistorialProyectosCalidad')) {
        $('#tablelHistorialProyectosCalidad').DataTable().destroy();
    }

    tabla = $("#tablelHistorialProyectosCalidad").DataTable({
        processing: true,
        serverSide: false,
        paging: true,
        searching: true,
        lengthChange: false,
        colReorder: true,
        responsive: true,
        autoWidth: false,
        dom: 'frtip',
        ajax: {
            url: "../../../../../Controller/ctrProyectos.php?proy=get_proyectos_total_x_client_id",
            type: "POST",
            dataType: "json",
            dataSrc: "aaData",
            data: {
                client_id: client_id,
                mostrar_historico: mostrar
            },
            error: function (e) {
                console.log("response: " + e.responseText);
                console.log("status" + e.status);

            }
        },
        order: [
            [0, "desc"]
        ],
        language: {
            sProcessing: "Procesando..",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados..",
            sEmptyTable: "Ninguna tarea disponible en esta tabla",
            sInfo: "",
            sInfoEmpty: "",
            sInfoFiltered: "(Filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar: ",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        }
    });
}

function verProyPorIdCliente(client_id) {
    $("#mostrar_historico").prop("checked", false);
    $("#ModalHistorialProyectosCalidad").modal("show");
    $("#inputHiddenIdCliente").val(client_id);

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_nombre_proyectos_total_x_client_id", {
        client_id: client_id
    }, function (data) {
        $("#idCliente").text(data[0].cliente);
    }, "json");

    initTablaHistorial(client_id, 0);

    $("#mostrar_historico").off("change").on("change", function () {
        initTablaHistorial(client_id, $(this).is(":checked") ? 1 : 0);
    });
}

function crearRechequeo(id) {
    $.post(
        "../../../../../Controller/ctrProyectos.php?proy=get_datos_para_insert_rechequeo", {
            id: id
        },
        function (data) {
            let ID_ORIGINAL = data.id;
            let POSICION_RECURRENCIA = data.posicion_recurrencia;

            Swal.fire({
                icon: "warning",
                text: "Desea crear un Retest de este Proyecto?",
                showCancelButton: true,
                confirmButtonText: "Sí, crear",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "question",
                        title: "Tipo de Retest",
                        text: "¿Qué tipo de Retest desea crear?",
                        showCancelButton: true,
                        confirmButtonText: "Retest Completo",
                        cancelButtonText: "Retest sobre Hallazgos",
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#f39c12",
                    }).then((tipoResult) => {

                        let tipo_rechequeo = "";

                        if (tipoResult.isConfirmed) {
                            tipo_rechequeo = "COMPLETO";
                        } else if (tipoResult.isDismissed && tipoResult.dismiss === Swal.DismissReason.cancel) {
                            tipo_rechequeo = "HALLAZGOS";
                        } else {
                            return;
                        }

                        console.log("Tipo seleccionado:", tipo_rechequeo);

                        // Limpieza de datos innecesarios
                        delete data.id;
                        delete data.fech_crea;
                        delete data.est;
                        delete data.fech_inicio;
                        delete data.fech_fin;
                        delete data.recurrencia;

                        if (data.id_proyecto_recurrencia === null) data.id_proyecto_recurrencia = 0;
                        if (data.archivo === null) data.archivo = "";

                        // Agregamos lo que el backend necesita
                        data.id_proyecto_gestionado = ID_ORIGINAL;
                        data.posicion_recurrencia = POSICION_RECURRENCIA;
                        data.id_proyecto_gestionado_origen = id;
                        data.tipo_rechequeo = tipo_rechequeo;

                        // Inserto el rechequeo
                        $.post(
                            "../../../../../Controller/ctrProyectos.php?proy=insert_rechequeo",
                            data,
                            function (resp) {
                                console.log("Respuesta insert:", resp);
                                if (resp.status === "success") {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Bien",
                                        text: "Retest creado correctamente",
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    setTimeout(() => {
                                        if ($.fn.DataTable.isDataTable('#tablelHistorialProyectosCalidad')) {
                                            $('#tablelHistorialProyectosCalidad').DataTable().ajax.reload(null, false);
                                            $("#table_proyectos_total_calidad").DataTable().ajax.reload(null, false);
                                            $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                                        }
                                    }, 1000);
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: resp.msg
                                    });
                                }
                            },
                            "json"
                        );
                    });
                }
            });
        },
        "json"
    );
}

function descargarExcel(client_id) {
    window.location.href = `../../../../../Controller/ctrReportes.php?case=excel&client_id=${client_id}`;
}

function mdlDescargarExcelProyectosTotal() {
    document.getElementById("formDescargarReporteXlsx").reset();
    $("#modalDescargarExcelProyectosTotal").modal("show");
}

function mdlDescargarExcelProyectosCrossSell() {
    document.location.href = `../../../../../Controller/ctrReportes.php?case=reporteExcelProyectosCrossSell`;
}

const params = new URLSearchParams(window.location.search);
if (params.get('doc') === "error") {
    Swal.fire({
        icon: "warning",
        title: "Error",
        text: "No se encontraron registros en esa fecha",
        showCancelButton: false,
        showConfirmButton: false,
        timer: 1500
    });

    // ✅ Eliminar el parámetro de la URL sin recargar
    const url = window.location.origin + window.location.pathname;
    history.replaceState({}, document.title, url);
}

//--------------------------------------------------------------------------------//

// Variable global para guardar los datos cargados
let dataRecurrente = null;

//------------------------------ INICIO RECURRENCIAS  -----------------------------------//
function gestionar_proy_recurrente(id_proyecto_cantidad_servicios, conteo_id_recurrencia) {

    $("#ModalPasarRecurrenteABorrador").modal("show");

    $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_ver_recurrente", {
            id_proyecto_cantidad_servicios: id_proyecto_cantidad_servicios
        },
        function (data, textStatus, jqXHR) {
            $("#contenido_proyecto_gestionado_para_insert_recurrente").html(data)
        },
        "html"
    );

    // Traigo datos para el insert
    $.ajax({
        type: "POST",
        url: "../../../../../Controller/ctrProyectos.php?proy=get_datos_recurrente_para_insert",
        data: {
            id_proyecto_cantidad_servicios: id_proyecto_cantidad_servicios
        },
        dataType: "json",
        success: function (response) {

            dataRecurrente = {
                id_proyecto_gestionado: response.id_proyecto_gestionado,
                id_proyecto_cantidad_servicios: response.id_proyecto_cantidad_servicios,
                cat_id: response.cat_id,
                cats_id: response.cats_id,
                sector_id: response.sector_id,
                usu_crea: response.usu_crea,
                prioridad_id: response.prioridad_id,
                hs_dimensionadas: response.hs_dimensionadas,
                estados_id: 14,
                titulo: response.titulo,
                descripcion: response.descripcion,
                refProy: response.refProy,
                recurrencia: response.recurrencia - 1,
                fech_vantive: response.fech_vantive,
                archivo: response.archivo,
                captura_imagen: response.captura_imagen,
                fech_crea: response.fech_crea,
                est: response.est
            };

        },
        error: function (xhr, status, error) {
            console.error("Error al obtener datos:", error);
        }
    });

}

function asignarPm(id_proyecto_gestionado, id_pm_calidad) {
    if (id_pm_calidad && id_pm_calidad !== 'null' && id_pm_calidad !== 'undefined') {
        Swal.fire({
            icon: "info",
            title: "Atencion",
            text: "Desea asignarse como PM de este proyecto?",
            showConfirmButton: true,
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../../../../Controller/ctrProyectos.php?proy=insert_nuevo_pm", {
                        id_pm_calidad: id_pm_calidad,
                        id_proyecto_gestionado: id_proyecto_gestionado

                    },
                    function (data, textStatus, jqXHR) {

                    },
                    "json"
                );
                setTimeout(() => {
                    if ($.fn.DataTable.isDataTable('#table_proyectos_en_proceso')) {
                        $('#table_proyectos_en_proceso').DataTable().ajax.reload(null, false);
                        $('#table_proyectos_borrador').DataTable().ajax.reload(null, false);
                        $("#table_proyectos_realizados").DataTable().ajax.reload(null, false);
                    }
                }, 100);
                Swal.fire({
                    icon: "success",
                    title: "Bien",
                    text: "Asignado correctamente",
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        })
    } else {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se puede asignar como PM a este proyecto",
            showConfirmButton: false,
            showCancelButton: true
        })
    }

}

$("#btnPasarRecurrenteABorrador").off("click").on("click", function () {
    if (!dataRecurrente) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Los datos aún no están listos"
        });
        return;
    } else {
        console.log(dataRecurrente);
    }

    // Insert recurrente
    $.ajax({
        type: "POST",
        url: "../../../../../Controller/ctrProyectos.php?proy=insert_recurrente_proy_gestionado",
        data: dataRecurrente,
        dataType: "json",
        success: function (response) {
            if (response.Status === "success") {
                const idPG = response.id_proyecto_gestionado; // 🔑 ID real recién creado

                $.post("../../../../../Controller/ctrProyectos.php?proy=insertar_usuarios_a_recurrente", {
                        id_proyecto_gestionado: idPG,
                        usu_asignado: null
                    },
                    function (data, textStatus, jqXHR) {

                    },
                    "json"
                );

                // Insert dimensionamiento
                $.ajax({
                    type: "POST",
                    url: "../../../../../Controller/ctrProyectos.php?proy=insert_dimensionamiento_recurrente_proy_gestionado",
                    data: {
                        id_proyecto_gestionado: idPG,
                        hs_dimensionadas: dataRecurrente.hs_dimensionadas,
                        usu_crea: dataRecurrente.usu_crea
                    },
                    dataType: "json",
                    success: function (res2) {

                        // ✅ Feedback al usuario
                        Swal.fire({
                            icon: "success",
                            title: "Bien",
                            text: "Proyecto pasado a borrador",
                            timer: 1100,
                            showCancelButton: false,
                            showConfirmButton: false
                        });

                        // ✅ Refresco tabla si existe
                        if ($.fn.DataTable.isDataTable('#table_proyectos_recurrencia')) {
                            $('#table_proyectos_recurrencia').DataTable().ajax.reload(null, false);
                        }

                        // ✅ Recarga total de la página
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    }
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al insertar:", error);
        }
    });
});

//------------------------------ FIN RECURRENCIAS  -----------------------------------//