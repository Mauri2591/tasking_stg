<?php
require_once __DIR__ . "/../../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    // require_once __DIR__ . "/../../../../../Model/Clases/Openssl.php";
    require_once __DIR__ . "/../../../../../Model/Clases/Headers.php";
    require_once __DIR__ . "/../../../../../Model/Clases/Redirect.php";

    Headers::get_cors();

    $params = Redirect::validateProyectoParams();
    $p_id  = $params['p_id'];
    $pg_id = $params['pg_id'];
?>

    <?php
    include_once __DIR__ . "/../../../../../View/Home/Public/Template/head.php";
    include_once __DIR__ . "/../../../../../View/Home/Public/Template/head.php";
    include_once __DIR__ . "/../../../../../View/Home/Public/Template/main_content.php";



    ?>
    <div class="page-content">
        <div class="container-fluid">

            <?php include_once __DIR__ . "/../Modales/mdlAgregarUsuarioProy.php";
            include_once __DIR__ . "/../Modales/mdlPipeline.php";
            ?>

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0"><span class="badge bg-warning text-dark border border-dark"></span></h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->
        <div class="col-lg-12">
            <div class="card-body d-flex bg-light p-0">
                <div class="col-lg-12 border-1 border border-primary">
                    <div class="card" style="height: 100%;">
                        <span id="titulo_servicio" class="badge bg-primary fw-bold text-light p-2 fs-12"></span>
                        <div class="card-body p-0">
                            <div class="text-muted">

                                <div class="border-top border-top-dashed">
                                </div>

                                <div class="col-lg-12 p-2 mt-1">
                                    <div class="row d-flex justify-content-evenly">
                                        <div class="col-ms-2 col text-center">
                                            <span type="button"
                                                onclick="copiar_ips(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2">Ips<i
                                                    class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px;  min-height: 220px; overflow-y: scroll; border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_ip"></div>
                                            </div>
                                        </div>

                                        <div class="col-ms-4 col text-center">
                                            <span type="button"
                                                onclick="copiar_urls(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2">Urls
                                                <i class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px; min-height: 220px;overflow-y: scroll;border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_url"></div>
                                            </div>
                                        </div>

                                        <div class="col-ms-2 col text-center">
                                            <span type="button"
                                                onclick="copiar_otros(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2 ">Otros<i
                                                    class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px; min-height: 220px; overflow-y: scroll;border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_otro"></div>
                                            </div>
                                        </div>

                                        <div class="col-xl-5 bg-success" style="margin-right: 5px; border-radius: 5px;">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center mt-1">
                                                    <span id="prioridad" class="badge mx-1" style="width: 3rem;"></span>
                                                    <span id="titulo_categoria"
                                                        class="badge bg-light text-dark border border-primary mx-2"></span>
                                                    <span id="titulo_subCategoria"
                                                        class="badge bg-light text-dark border border-primary mx-2"></span>
                                                    <span
                                                        class="me-2 badge bg-light text-primary border border-primary">Desde:
                                                        <span id="fech_inicio" class="text-dark"></span>
                                                    </span>
                                                    <span
                                                        class="me-2 badge bg-light text-primary border border-primary">Hasta:
                                                        <span id="fech_fin" class="text-dark"></span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div style="display: flex; margin-top: .2rem; margin-bottom: .2rem;">
                                                <span id="rechequeo" style="display: none; color:orangered" class="badge ml-1 border border-dark bg-light"></span>
                                                <span id="proy_recurrencia" style="display: none; color:orangered" class="badge ml-1 border border-dark bg-light"></span>
                                                <span id="workshop" style="display: flex;" class="badge mx-1 text-light border border-dark bg-info">workshop</span>
                                                <span class="badge bg-light border border-dark text-dark mx-2 mb-1">Ref: <span id="referencia_proy"></span></span>
                                                <span class="badge bg-light border border-dark text-dark mx-2 mb-1">Detalle:</span>
                                            </div>

                                            <div class="card-body p-0">
                                                <div data-simplebar="init" style="max-height: 200px;">
                                                    <div class="simplebar-wrapper"
                                                        style="margin: 0px; text-justify: distribute;">
                                                        <div class="simplebar-height-auto-observer-wrapper">
                                                            <div class="simplebar-height-auto-observer"></div>
                                                        </div>
                                                        <div class="simplebar-mask">
                                                            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                                <div class="simplebar-content-wrapper" tabindex="0"
                                                                    role="region" aria-label="scrollable content">
                                                                    <div class="simplebar-content bg-light"
                                                                        style="padding: 10px; border-radius: 5px;">
                                                                        <p style="font-size: 11.5px; max-height: 160px;  min-height: 160px; overflow: hidden scroll;"
                                                                            class="text-dark mb-2"
                                                                            id="parrafo_descripcion_proy">
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="simplebar-placeholder"
                                                            style="width: auto; height: 339px;"></div>
                                                    </div>
                                                    <div class="simplebar-track simplebar-horizontal"
                                                        style="visibility: hidden;">
                                                        <div class="simplebar-scrollbar" style="width: 0px; display: none;">
                                                        </div>
                                                    </div>
                                                    <div class="simplebar-track simplebar-vertical"
                                                        style="visibility: visible;">
                                                        <div class="simplebar-scrollbar"
                                                            style="height: 194px; transform: translate3d(0px, 0px, 0px); display: block;">
                                                        </div>
                                                    </div>
                                                </div><!-- end card body -->
                                            </div><!-- end card -->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 row">
                                    <div class="col-xl-10">
                                        <span
                                            class="badge bg-dark text-success mx-2 mt-2 mb-3 d-inline-flex align-items-center gap-2 w-auto">Imagen:</span>
                                        <div class="card">
                                            <div class="card-body p-0 " id="cont_imagen">
                                                <div data-simplebar="init" style="min-height: 460px;">
                                                    <div class="simplebar-wrapper" style="margin: 0px;">
                                                        <div class="simplebar-height-auto-observer-wrapper">
                                                            <div class="simplebar-height-auto-observer"></div>
                                                        </div>
                                                        <div class="simplebar-mask">
                                                            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                                <div class="simplebar-content-wrapper" tabindex="0"
                                                                    role="region" aria-label="scrollable content"
                                                                    style="height: auto; overflow: hidden scroll; overflow-x: scroll;">
                                                                    <div class="simplebar-content" style="padding: 10px;">
                                                                        <div id="img_proy">

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="simplebar-placeholder"
                                                            style="width: auto; height: 339px;">
                                                        </div>
                                                    </div>
                                                    <div class="simplebar-track simplebar-horizontal"
                                                        style="visibility: hidden;">
                                                        <div class="simplebar-scrollbar" style="width: 0px; display: none;">
                                                        </div>
                                                    </div>
                                                    <div class="simplebar-track simplebar-vertical"
                                                        style="visibility: visible;">
                                                        <div class="simplebar-scrollbar"
                                                            style="height: 194px; transform: translate3d(0px, 0px, 0px); display: block;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                        <span
                                            class="badge bg-dark text-success mx-2 mt-2 mb-3 d-inline-flex align-items-center gap-2 w-auto">
                                            Documento:
                                            <span title="Descargar el documento adjunto en el proyecto"
                                                id="documento_proy"></span>
                                        </span>
                                        <br>

                                        <div id="contDescargarPipeline">

                                        </div>

                                    </div>
                                    <div class="col-xl-2 bg-success" style="max-height: 500px;border-radius: 5px;">
                                        <div style="min-height: 250px;">
                                            <span
                                                class="badge bg-light border border-dark text-dark mx-2 mt-2 mb-3 d-inline-flex align-items-center gap-1">
                                                Usuarios Asignados:
                                                <i id="boton_agregar_usuarios_proy"
                                                    onclick="agregarUsuario(<?php echo Openssl::get_ssl_decrypt($_GET['p']) ?>)"
                                                    type="button" class="ri-user-add-line text-danger fs-5"
                                                    title="Agregar usuarios" style="cursor: pointer;"></i>
                                            </span>

                                            <ul class="bg-light border border-light"
                                                style="max-height: 12rem; border-radius: 5px; overflow-y: scroll;"
                                                id="ul_proy_eh">
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-top border-top-dashed mt-4">
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled vstack gap-3 mb-0">
                                            <li id="cont_descripciones_proyecto">

                                            </li>
                                        </ul>
                                        <section class="p-2 mt-2" id="sect_descrip">
                                            <textarea name="descripcion_proyecto" id="descripcion_proyecto"
                                                class="d-none"></textarea>

                                            <section class="d-flex">
                                                <label class="form-control fs-11" style="width: 30%;" for="captura_imagen">
                                                    Captura de
                                                    Imagen:
                                                    <input class="form-control-sm" type="text"
                                                        placeholder="Ingrese la captura de imagen" id="captura_imagen"
                                                        name="captura_imagen">
                                                </label>
                                                <label class="form-control fs-11" style="width: 70%;" for="archivo">Subir
                                                    Informes:
                                                    <input class="form-control-sm" type="file" id="documento" multiple
                                                        name="documento[]"
                                                        accept=".pdf, .doc, .docx, .xls, .xlsx, .jpg, .jpeg, .png, .txt, .zip">
                                                </label>
                                            </section>
                                            <div class="col-12" id="cont_mje_proy_archivo">

                                            </div>
                                            <br>
                                            <section style="display: flex;">
                                                <button id="btn_guardar_descripcion"
                                                    class="btn btn-sm btn-primary text-light" type="button">
                                                    <span id="btn_texto_guardar">Guardar</span>
                                                    <span id="btn_spinner_guardar"
                                                        class="spinner-border spinner-border-sm ms-2 d-none" role="status"
                                                        aria-hidden="true"></span>
                                                </button>

                                                <button id="btn_finalizar_proyecto"
                                                    class="btn btn-sm btn-success text-light">Finalizar</button>
                                            </section>

                                        </section>
                                        <section id="cont_usuario_finalizador" style="font-size: 11px; display: none;"
                                            class="ms-2 badge bg-dark text-success border border-success">


                                        </section>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page-content -->
        <?php
        include_once __DIR__ . "/../../../../../View/Home/Public/Template/footer.php";
        ?>
    <?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
} ?>

    <script>
        var id_proyecto_gestionado =
            "<?php echo isset($_GET['pg']) ? Openssl::get_ssl_decrypt($_GET['pg']) : ''; ?>"

        var pg =
            "<?php echo isset($_GET['pg']) ? $_GET['pg'] : ''; ?>"

        var sector_usu_id =
            "<?php echo isset($_SESSION['sector_id']) ? $_SESSION['sector_id'] : "" ?>";

        var id_proyecto_cantidad_servicios =
            "<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>";

        var session_usu_id =
            "<?php echo isset($_SESSION['usu_id']) ? $_SESSION['usu_id'] : "" ?>";

        document.addEventListener("DOMContentLoaded", function() {
            let btn_guardar_descripcion = document.getElementById("btn_guardar_descripcion");

            let btn_finalizar_proyecto = document.getElementById("btn_finalizar_proyecto");

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_descripciones_proyecto", {
                    id: id_proyecto_gestionado
                },
                function(data, textStatus, jqXHR) {
                    $("#cont_descripciones_proyecto").html(data);

                },
                "html"
            );



            $.post("../../../../../Controller/ctrProyectos.php?proy=validar_boton_mostrar_agregar_usuario_proy", {
                id_proyecto_gestionado: id_proyecto_gestionado
            }, function(data) {
                let parsed = JSON.parse(data); // Ahora parsed es "ok" sin comillas extra

                if (parsed === "ok") {
                    document.getElementById("boton_agregar_usuarios_proy").style.display = "flex";
                } else if (parsed === "error") {
                    document.getElementById("boton_agregar_usuarios_proy").style.display = "none";
                }
            });

            function validar_boton_usuario_asignado_y_calidad() {
                $.post("../../../../../Controller/ctrProyectos.php?proy=validar_boton_usuario_asignado_y_calidad", {
                    id_proyecto_gestionado: id_proyecto_gestionado
                }, function(data) {
                    let mostrar = false;
                    if (sector_usu_id == "4") {
                        mostrar = true;
                    } else {
                        mostrar = false;
                    }

                    data.forEach(elem => {
                        if (elem.usu_asignado == session_usu_id || sector_usu_id == "4") {
                            mostrar = true;
                        }
                        if (elem.estados_id == "3" || elem.estados_id == "4" || elem.estados_id ==
                            "14" || elem.estados_id == "15" || elem.estados_id == "16" || elem
                            .estados_id == "17") {
                            mostrar = false;
                            $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_usuario_finalizador_proyecto", {
                                    id_proyecto_gestionado: id_proyecto_gestionado
                                },
                                function(data, textStatus, jqXHR) {
                                    document.getElementById("cont_usuario_finalizador").style
                                        .display = "block";
                                    $("#cont_usuario_finalizador").html(data)
                                },
                                "html"
                            );
                        }
                    });
                    const contenedor = document.getElementById("sect_descrip");
                    if (contenedor) {
                        contenedor.style.display = mostrar ? "block" : "none";
                    }
                }, "json");
            }

            validar_boton_usuario_asignado_y_calidad()



            $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_proyecto_gestionado", {
                    id: id_proyecto_gestionado
                },
                function(data, textStatus, jqXHR) {
                    console.log(data);

                    $("#referencia_proy").text(data.refProy)

                    $.post("../../../../../Controller/ctrProyectos.php?proy=validarContenedorBtnDockerfile", {
                            id: id_proyecto_gestionado,
                            pg: pg
                        },
                        function(data, textStatus, jqXHR) {
                            $("#contDescargarPipeline").html(data);
                        },
                    );

                    if (data.workshop == "SI") {
                        document.getElementById("workshop").style.display = "flex";
                    } else {
                        document.getElementById("workshop").style.display = "none";
                    }

                    if (data.posicion_recurrencia) {
                        document.getElementById("proy_recurrencia").style.display = "flex";
                        $("#proy_recurrencia").text("Recurrente: " + data.posicion_recurrencia);
                    } else {
                        document.getElementById("proy_recurrencia").style.display = "none";
                    }


                    if (data.rechequeo == "SI") {
                        document.getElementById("rechequeo").style.display = "flex";
                        $("#rechequeo").text("Rechequeo");
                    } else {
                        document.getElementById("rechequeo").style.display = "none";
                    }

                    $("#prioridad")
                        .removeClass("border-success border-warning border-danger")
                        .text("");
                    switch (data.prioridad_id) {
                        case 1:
                            $("#prioridad").addClass("border border-dark bg-light text-success").text(
                                "Baja");
                            break;
                        case 2:
                            $("#prioridad").addClass("bg-warning text-dark").text("Media");
                            break;
                        case 3:
                            $("#prioridad").addClass("bg-danger text-light").text("Alta");
                            break;
                        default:
                            "";
                            break;
                    }

                    let subCatNom = data.cats_nom || "";
                    let subCatTruncado = subCatNom.length > 30 ?
                        subCatNom.substring(0, 27) + "..." :
                        subCatNom;
                    $("#titulo_subCategoria")
                        .text(subCatTruncado)
                        .attr("title", subCatNom);
                    if (data.captura_imagen == null) {
                        $("#fech_inicio").text(data.fech_inicio);
                        $("#fech_fin").text(data.fech_fin);
                        let captura_imagen = `<p>Sin imagen</p>`;
                        $("#img_proy").html(captura_imagen);
                        document.getElementById("cont_imagen").style.display = "none";
                    } else {
                        if (data.archivo) {
                            let archivo = data.archivo;
                            let li_archivo = `<a href="${URL}/View/Home/Public/Uploads/Calidad/${archivo}" download target="_blank">
                                            <i class=" ri-file-excel-2-fill text-success" style="font-size:24px"></i>
                                        </a>`;
                            $("#documento_proy").html(li_archivo)
                        } else {
                            $("#documento_proy").text("No posee")

                        }

                        $("#fech_inicio").text(data.fech_inicio);
                        $("#fech_fin").text(data.fech_fin);
                        if (data.captura_imagen) {
                            if (data.captura_imagen && data.captura_imagen.startsWith("data:image")) {
                                $("#img_proy").html(
                                    `<img src="${data.captura_imagen}" alt="Captura" style="min-width: 100%; min-height: 100%; border: 1px solid #ccc; border-radius: 5px;">`
                                );
                            }
                            document.getElementById("cont_imagen").style.display = "block";
                        } else {
                            $("#img_proy").text("No hay imagen")
                        }
                    }
                    let CatNom = data.cat_nom || "";
                    let CatTruncado = CatNom.length > 14 ?
                        CatNom.substring(0, 12) + ".." :
                        CatNom;
                    $("#titulo_categoria")
                        .text(CatTruncado)
                        .attr("title", CatNom);

                    $("#titulo_servicio").text(data.titulo);
                    $("#parrafo_descripcion_proy").text(data.descripcion);
                }, "json");

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_usuarios_x_proy_y_sector", {
                    id_proyecto_cantidad_servicios: id_proyecto_gestionado
                },
                function(data, textStatus, jqXHR) {
                    $("#ul_proy_eh").html(data)
                },
                "html"
            );

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_ip", {
                    id_proyecto_gestionado: id_proyecto_gestionado
                },
                function(data, textStatus, jqXHR) {
                    if (data) {
                        $("#cont_ip").html(data)
                    } else {
                        $("#cont_ip").text("No hay ips")
                    }
                },
                "html"
            );

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_url", {
                    id_proyecto_gestionado: id_proyecto_gestionado
                },
                function(data, textStatus, jqXHR) {
                    if (data) {
                        $("#cont_url").html(data)
                    } else {
                        $("#cont_url").text("No hay url's")
                    }
                },
                "html"
            );

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_otro", {
                    id_proyecto_gestionado: id_proyecto_gestionado
                },
                function(data, textStatus, jqXHR) {
                    if (data) {
                        $("#cont_otro").html(data)
                    } else {
                        $("#cont_otro").text("No hay activos")
                    }
                },
                "html"
            );

            function summernote() {
                $('#descripcion_proyecto').summernote({
                    height: 150,
                    placeholder: 'Ingrese su consulta',
                    codemirror: {
                        theme: 'monokai'
                    },
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['misc', ['undo', 'redo']]
                    ],
                    callbacks: {
                        onInit: function() {
                            $('#descripcion_proyecto').removeClass('d-none');
                        },
                        onPaste: function(e) {
                            let clipboardData = (e.originalEvent || e).clipboardData || window
                                .clipboardData;
                            let pastedData = clipboardData.getData('text/html');

                            if (pastedData && pastedData.includes('src="data:image')) {
                                e.preventDefault();
                                Swal.fire({
                                    icon: 'warning',
                                    title: "Error!",
                                    text: "No se permiten capturas de pantalla desde este campo!",
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    timer: 1300
                                });
                            }
                        },
                        onImageUpload: function() {
                            Swal.fire({
                                icon: 'warning',
                                title: "Error!",
                                text: "No se permiten capturas de pantalla desde este campo!",
                                showConfirmButton: false,
                                showCancelButton: false,
                                timer: 1300
                            });
                        }
                    }
                });
            }
            summernote();

            function captura_imagen_b64() {
                document.getElementById("captura_imagen").addEventListener("paste", function(e) {
                    let clipboardData = (e.clipboardData || window.clipboardData);

                    // Buscar si hay items tipo imagen
                    let items = clipboardData.items;
                    let foundImage = false;

                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type.indexOf("image") !== -1) {
                            let file = items[i].getAsFile();
                            let reader = new FileReader();
                            reader.onload = function(event) {
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

            function data_insert_descripciones_proyecto() {
                let formData = new FormData();

                let documentoInput = document.getElementById("documento");
                let archivos = documentoInput.files;

                formData.append('id_proyecto_cantidad_servicios', id_proyecto_cantidad_servicios);
                formData.append('id_proyecto_gestionado', id_proyecto_gestionado)
                formData.append('descripcion_proyecto', document.getElementById("descripcion_proyecto").value);
                formData.append('captura_imagen', document.getElementById("captura_imagen").value);
                for (let i = 0; i < archivos.length; i++) {
                    formData.append('documento[]', archivos[i]);
                }
                return formData;
            }


            document.getElementById("btn_guardar_descripcion").addEventListener("click", function() {
                const btn = document.getElementById("btn_guardar_descripcion");
                const spinner = document.getElementById("btn_spinner_guardar");
                const textoBtn = document.getElementById("btn_texto_guardar");

                // 🔹 Activar siempre el spinner y "Guardando"
                textoBtn.textContent = "Guardando";
                spinner.classList.remove("d-none");
                btn.disabled = true;

                let data = data_insert_descripciones_proyecto();
                let descripcion = data.get('descripcion_proyecto');

                if (!descripcion || descripcion.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: "Error!",
                        text: "Datos vacíos",
                        timer: 1100,
                        showConfirmButton: false
                    });

                    // 🔹 IMPORTANTE: Restaurar el botón si no hay descripción
                    spinner.classList.add("d-none");
                    textoBtn.textContent = "Guardar";
                    btn.disabled = false;
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "../../../../../Controller/ctrProyectos.php?proy=insert_descripciones_proyecto",
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const btn = document.getElementById("btn_guardar_descripcion");
                        const spinner = document.getElementById("btn_spinner_guardar");
                        const textoBtn = document.getElementById("btn_texto_guardar");

                        // Activar spinner y texto "Guardando"
                        textoBtn.textContent = "Guardando";
                        spinner.classList.remove("d-none");
                        btn.disabled = true;

                        let json;
                        try {
                            json = typeof response === 'string' ? JSON.parse(response) :
                                response;
                        } catch (e) {
                            console.error("Respuesta inválida del servidor:", response);
                            return;
                        }

                        // ⚠️ Caso: archivos inválidos pero nota guardada
                        if (json.status === "error" && json.errores) {
                            // Mostrar alerta HTML de error MIME
                            let htmlErrores =
                                `<div class="alert alert-warning text-center" role="alert"><strong>Error:</strong><br>`;
                            json.errores.forEach(err => {
                                htmlErrores += `- ${err}<br>`;
                            });
                            htmlErrores += `</div>`;
                            $("#cont_mje_proy_archivo").html(htmlErrores).show();
                            $("#documento").val("");

                            // ⏱ Ocultar error después de 1.5s, luego mostrar Swal, luego recargar
                            setTimeout(() => {
                                $("#cont_mje_proy_archivo").fadeOut();

                                Swal.fire({
                                    icon: 'info',
                                    title: "Guardado parcial",
                                    text: "La nota fue guardada, pero algunos archivos no se subieron.",
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // ⏱ Recargar luego del Swal
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }, 1500);

                            return;
                        }

                        // ✅ Todo OK
                        Swal.fire({
                            icon: 'success',
                            title: "Bien!",
                            text: json.mensaje,
                            timer: 1100,
                            showConfirmButton: false
                        });

                        $("#captura_imagen").val('');
                        $('#descripcion_proyecto').summernote('reset');

                        setTimeout(() => {
                            location.reload();
                        }, 1100);
                    },
                    error: function() {
                        let htmlmje = `<div id="extension_no_permitida" class="alert alert-warning text-center" role="alert">
                <a class="alert-link">Error! <br></a>Extensión no permitida
            </div>`;
                        $("#cont_mje_proy_archivo").html(htmlmje).show();
                        $("#documento").val("");
                        setTimeout(() => {
                            $("#cont_mje_proy_archivo").fadeOut();
                        }, 2000);

                        // En error también dejamos "Guardando" hasta el reload
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                });
            });


            function finalizar_proyecto(id_proyecto_gestionado) {
                btn_finalizar_proyecto.addEventListener("click", function() {
                    Swal.fire({
                        icon: 'info',
                        title: "¿Desea finalizar este Proyecto?",
                        text: 'Presione OK para continuar',
                        showCancelButton: true,
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.post("../../../../../Controller/ctrProyectos.php?proy=update_proyecto_desarrollo_tasking", {
                                    id_proyecto_gestionado: id_proyecto_gestionado
                                },
                                function(data, textStatus, jqXHR) {

                                },
                                "json"
                            );

                            $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_proyecto_gestionado", {
                                id: id_proyecto_gestionado
                            }, function(data) {

                                if (data.fech_fin == null || data.fech_fin == '') {
                                    Swal.fire({
                                        icon: 'info',
                                        title: "El proyecto no posee FECHA DE FINALIZACION",
                                        text: 'Presiona OK y se le asignará la de hoy',
                                        showCancelButton: true,
                                        showConfirmButton: true
                                    }).then((resultFecha) => {
                                        if (resultFecha.isConfirmed) {
                                            const today = new Date();
                                            const fechaHoy = today.getFullYear() +
                                                "-" +
                                                String(today.getMonth() + 1)
                                                .padStart(2, '0') + "-" +
                                                String(today.getDate()).padStart(2,
                                                    '0');

                                            $.post("../../../../../Controller/ctrProyectos.php?proy=asignar_fecha_proyecto_finalizado_sin_fecha_fin", {
                                                id: data.id,
                                                fech_fin: fechaHoy
                                            }, function(resp) {
                                                if (resp.Status === "OK") {
                                                    finalizarProyectoAjax(
                                                        id_proyecto_gestionado
                                                    );
                                                    $.post("../../../../../../Controller/ctrProyectos.php?proy=finalizar_proyecto", {
                                                            estados_id: 3,
                                                            id_proyecto_gestionado: id_proyecto_gestionado
                                                        },
                                                        function(data,
                                                            textStatus,
                                                            jqXHR) {},
                                                        "json"
                                                    );
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: "Error",
                                                        text: "No se pudo guardar la fecha. Intente nuevamente.",
                                                    });
                                                }
                                            }, "json").fail(function(err) {
                                                console.log(err);

                                            });
                                        }
                                    });
                                } else {
                                    finalizarProyectoAjax(id_proyecto_gestionado);
                                }
                            }, "json").fail(function(err) {
                                console.error(err);
                            });
                        }
                    });
                });

                function finalizarProyectoAjax(id) {
                    console.log("Finalizando proyecto con ID:", id);

                    $.post("../../../../../Controller/ctrProyectos.php?proy=finalizar_proyecto", {
                        estados_id: 3,
                        id_proyecto_gestionado: id
                    });

                    $.post("../../../../../Controller/ctrProyectos.php?proy=finalizar_proyecto_tabla_estados_proyecto", {
                        id_proyecto_gestionado: id,
                        estados_id: 3
                    });

                    Swal.fire({
                        icon: 'success',
                        title: "Bien",
                        text: 'Proyecto finalizado correctamente!',
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 1100
                    });

                    $('#table_proyectos_abiertos_eh_pentest').DataTable().ajax.reload(null, false);
                    $('#table_proyectos_realizados_eh_pentest').DataTable().ajax.reload(null, false);

                    setTimeout(() => {
                        window.location.reload();
                    }, 1100);
                }
            }
            finalizar_proyecto(id_proyecto_gestionado)
        });

        function descargarPipeline(id) {
            $("#ModalVerTecnologiasPipeline").modal("show");
            $.post("../../../../../Controller/ctrProyectos.php?proy=getDatosCliente", {
                    id: id
                },
                function(data, textStatus, jqXHR) {
                    $("#id_proyecto_gestionado_pipeline").val(data.id);
                    $("#refProy_pipeline").val(data.refProy);
                    $("#client_rs_pipeline").val(data.client_rs);
                },
                "json"
            );
        }


        function agregarUsuario() {
            $("#ModalAgregarUsuarioProy").modal("show");

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_sector_x_proy", {
                    id: id_proyecto_gestionado
                },
                function(data, textStatus, jqXHR) {

                    let SECTOR_ID = data.sector_id;
                    $.post("../../../../../Controller/ctrProyectos.php?proy=get_usuarios_x_sector_agregar_a_proy", {
                            sector_id: SECTOR_ID
                        },
                        function(data, textStatus, jqXHR) {
                            $("#combo_usuarios_agregar_proy").html(data)

                        },
                        "html"
                    );

                },
                "json"
            );
        }

        function insert_usuarios_proyecto() {
            $.post("../../../../../Controller/ctrProyectos.php?proy=insert_usuarios_proyecto_abierto", {
                    id_proyecto_gestionado: id_proyecto_gestionado,
                    usu_asignado: $("#combo_usuarios_agregar_proy").val()
                },
                function(data, textStatus, jqXHR) {

                },
                "json"
            );

            setTimeout(() => {
                $("#ModalAgregarUsuarioProy").modal("hide");

            }, 1000);

            Swal.fire({
                icon: "success",
                title: "Bien",
                text: "Usuario agregado correctamente",
                timer: 1200,
                showCancelButton: false,
                showConfirmButton: false
            });
            setTimeout(() => {
                window.location.reload();
            }, 1100);
        }

        function eliminar_descripcion(id) {
            Swal.fire({
                icon: "info",
                title: "Atencion!",
                text: "Desea eliminar esta nota?",
                showCancelButton: true,
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("../../../../../Controller/ctrProyectos.php?proy=delete_descripciones_proyecto", {
                            id: id
                        },
                        function(data, textStatus, jqXHR) {

                        },
                        "json"
                    );
                    Swal.fire({
                        icon: 'success',
                        title: "Bien",
                        text: "Nota eliminada correctamente!",
                        showConfirmButton: false,
                        showCancelButton: false,
                        timer: 1100
                    });
                    setTimeout(() => {
                        window.location.reload();
                    }, 1100);
                }
            })

        }

        function copiar_ips(id_proyecto_cantidad_servicios) {
            Toastify({
                text: "¡IPs copiadas!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#0ab39c",
            }).showToast();

            let contenido = document.getElementById("cont_ip").innerText.trim();
            navigator.clipboard.writeText(contenido).then(function() {
                toast.success('Successfully toasted!')
            }).catch(function(error) {
                console.error("Error al copiar: ", error);
            });
        }

        function copiar_urls(id_proyecto_cantidad_servicios) {
            Toastify({
                text: "Urls copiadas!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#0ab39c",
            }).showToast();

            let contenido = document.getElementById("cont_url").innerText.trim();
            navigator.clipboard.writeText(contenido).then(function() {
                toast.success('Successfully toasted!')
            }).catch(function(error) {
                console.error("Error al copiar: ", error);
            });
        }

        function copiar_otros(id_proyecto_cantidad_servicios) {
            Toastify({
                text: "¡Activos copiados!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#0ab39c",
            }).showToast();

            let contenido = document.getElementById("cont_otro").innerText.trim();
            navigator.clipboard.writeText(contenido).then(function() {
                toast.success('Successfully toasted!')
            }).catch(function(error) {
                console.error("Error al copiar: ", error);
            });
        }
    </script>