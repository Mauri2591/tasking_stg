const btnAltaActivo = document.getElementById("altaActivo");
const btnCopiarActivos = document.getElementById("copiarActivos");
$(document).ready(function () {
    tabla = $("#activos_calidad").dataTable({
        "aProcessing": true,
        "aServerSide": false,
        "dom": 'Bfrtip',
        "searching": true,
        "lengthChange": false,
        "colReorder": true,
        "buttons": ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
        "ajax": {
            url: "../../../../Controller/ctrGestionActivos.php?case=get_activos",
            type: "post",
            dataType: "json",
            dataSrc: "",
            cache: false
        },
        "columns": [{
        "data": "host",
        "width": "10%"
    },
    {
        "data": "ambiente",
        "width": "20%"
    },
    {
        "data": "sector",
        "width": "15%"
    },
    {
        "data": "alta",
        "width": "10%"
    },
    {
        "data": "usu_correo",
        "width": "15%"
    },
    // {
    //     "data": "id",
    //     "width": "20%",
    //     "render": function (data, type, row) {
    //         return `
    //             <button class="btn btn-sm btn-primary" onclick="editarActivo(${data})">
    //                 <i class="ri-edit-line"></i>
    //             </button>
    //             <button class="btn btn-sm btn-danger" onclick="eliminarActivo(${data})">
    //                 <i class="ri-delete-bin-line"></i>
    //             </button>
    //         `;
    //     }
    // }
],
        "ordering": false,
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando..",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados..",
            "sEmptyTable": "Ninguna tarea disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(Filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar: ",
            "sInfoThousands": ",",
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

// if (btnAltaActivo) {
//     btnAltaActivo.addEventListener("click", () => {
//         alert("En desa")
//     })
// }

if (btnCopiarActivos) {
    btnCopiarActivos.addEventListener("click", () => {
        const data = tabla.api().rows().data();

        // Crear tabla HTML
        let tabla_html = '<p style="font-weight:bold">Desde donde se realizarán las pruebas:</p><table border="1"><tr><th>Host</th><th>Ambiente</th><th>Sector</th></tr>';
        Array.from(data).forEach(row => {
            tabla_html += `<tr><td>${row.host}</td><td>${row.ambiente}</td><td>${row.sector}</td></tr>`;
        });
        tabla_html += '</table>';

        // Copiar al clipboard como HTML
        navigator.clipboard.write([
            new ClipboardItem({
                'text/html': new Blob([tabla_html], { type: 'text/html' }),
                'text/plain': new Blob([Array.from(data).map(row => `${row.host} - ${row.ambiente} - ${row.sector}`).join('\n')], { type: 'text/plain' })
            })
        ]).then(() => {
            Toastify({
                text: "Datos copiados!",
                duration: 1500,
                gravity: "top",
                position: "right",
                backgroundColor: "#0ab39c",
            }).showToast();
        }).catch(() => {
            alert("Error al copiar");
        });
    });
}



// function editarActivo(data) {
//     alert("editar: " + data)
// }

// function eliminarActivo(data) {
//     alert("eliminar: " + data)
// }