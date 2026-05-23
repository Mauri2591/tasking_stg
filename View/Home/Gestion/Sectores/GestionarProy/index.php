<?php
require_once __DIR__ . "/../../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    // require_once __DIR__ . "/../../../../../Model/Clases/Openssl.php";
    require_once __DIR__ . "/../../../../../Model/Clases/Headers.php";
    require_once __DIR__ . "/../../../../../Model/Clases/Redirect.php";
    require_once __DIR__ . "/../../../../../Model/Proyectos.php";

    Headers::get_cors();
    $params = Redirect::validateProyectoParams();
    $p_id  = $params['p_id'];
    $pg_id = $params['pg_id'];

    //Variables PHP locales para la vista----------------------------------------
    $proyecto = new Proyectos();
    $estado_id = $proyecto->get_estado_proyecto_gestionado($pg_id)['estados_id'];
    $total_estados = $proyecto->get_total_estados();

    $estado_actual = array_filter($total_estados, fn($e) => $e['estados_id'] == $estado_id);
    $estado_actual = reset($estado_actual);

    $validar_envio_correo = $proyecto->get_validar_si_tiene_correos_enviados($pg_id)['total']; //validar si tiene correos enviados
    $datos_correos_enviados = $proyecto->get_datos_correo_enviado($pg_id); //quien envio los correos
    $documentos_envio_cliente = $proyecto->get_documentos_para_envio_correo_cliente($pg_id); //validar documentos a enviar
    $client_rs = $proyecto->get_datos_cliente_para_envio_correo($pg_id)['client_rs']; //validar documentos a enviar
    $correo_envio_cliente = $proyecto->get_datos_cliente_para_envio_correo($pg_id)['correo_envio_cliente']; //validar documentos a enviar
    //----------------------------------------------------------------------------

?>
    <?php
    include_once __DIR__ . "/../../../../../View/Home/Public/Template/head.php";
    include_once __DIR__ . "/../../../../../View/Home/Public/Template/main_content.php";
    ?>

    <div class="page-content">
        <div class="container-fluid">

            <?php
            include_once __DIR__ . "/../Modales/mdlAgregarUsuarioProy.php";
            include_once __DIR__ . "/../Modales/mdlPipeline.php";
            include_once __DIR__ . "/../Modales/mdlLogsProyectos.php";
            include_once __DIR__ . "/../Modales/mdlEnviarCorreoCliente.php";
            ?>

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light">
                        <h4 class="mb-sm-0"><span class="badge bg-warning text-dark border border-dark"></span></h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>

        <?php
        if ($_SESSION['usu_id'] == "104") { //Usuario testing mio
            foreach ($_SESSION as $key => $val) {
                echo '<pre>' . $key . ':' . $val . '</pre>';
            }
            $param_p = Openssl::get_ssl_decrypt($_GET['p']);
            echo '<pre>id_proyecto_cantidad_servicios: <strong style="font-size:1rem">' . $param_p . '</strong></pre>';
            $param_pg = Openssl::get_ssl_decrypt($_GET['pg']);
            echo '<pre>id_proyecto_gestionado: <strong style="font-size:1rem">' . $param_pg . '</strong></pre>';
        }
        ?>

        <style>
            .table-osint {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }

            .table-osint thead th {
                background: #f5f5f5;
                padding: 10px;
                border-bottom: 2px solid #ddd;
                text-align: center;
            }

            .table-osint tbody td {
                padding: 8px 10px;
                border-bottom: 1px solid #eee;
                max-width: 250px;
                /* controla el ancho */
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* filas intercaladas */
            .table-osint tbody tr:nth-child(even) {
                background-color: #fafafa;
            }

            /* hover */
            .table-osint tbody tr:hover {
                background-color: #f0f0f0;
                transition: background 0.15s ease-in-out;
            }

            .spin {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>

        <!-- container-fluid -->
        <div class="col-lg-12">
            <div class="card-body d-flex bg-light p-0">
                <div class="col-lg-12" style="border:  .1rem solid #dfdfdf;">
                    <div class="card" style="height: 100%;">
                        <span id="titulo_servicio" class="badge bg-primary fw-bold text-light p-2 fs-12"></span>
                        <div class="card-body p-0">
                            <div class="text-muted">

                                <div class="border-top border-top-dashed">
                                </div>

                                <div class="col-lg-12 p-2 mt-1">
                                    <div id="contenedor_ips_y_descripcion" class="row d-flex justify-content-evenly" style="opacity:0;">

                                        <div id="contenedor_ips" style="display:none;" class="col-ms-2 col text-center">
                                            <span type="button"
                                                onclick="copiar_ips(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2">Ips<i
                                                    class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px;  min-height: 220px; overflow-y: scroll; border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_ip"></div>
                                            </div>
                                        </div>

                                        <div id="contenedor_urls" style="display:none;" class="col-ms-4 col text-center">
                                            <span type="button"
                                                onclick="copiar_urls(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2">Urls
                                                <i class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px; min-height: 220px;overflow-y: scroll;border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_url"></div>
                                            </div>
                                        </div>

                                        <div id="contenedor_dispositivos" style="display:none;" class="col-ms-2 col text-center">
                                            <span type="button"
                                                onclick="copiar_dispositivos(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2">Dispositivos<i
                                                    class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px;  min-height: 220px; overflow-y: scroll; border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_dispositivos"></div>
                                            </div>
                                        </div>

                                        <div id="contenedor_agentes" style="display:none;" class="col-ms-2 col text-center">
                                            <span type="button"
                                                onclick="copiar_agentes(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2">Agentes<i
                                                    class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px;  min-height: 220px; overflow-y: scroll; border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_agentes"></div>
                                            </div>
                                        </div>

                                        <div id="contenedor_equipos" style="display:none;" class="col-ms-2 col text-center">
                                            <span type="button"
                                                onclick="copiar_equipos(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2">Equipos<i
                                                    class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px;  min-height: 220px; overflow-y: scroll; border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_equipos"></div>
                                            </div>
                                        </div>

                                        <div id="contenedor_otros" style="display:none;" class="col-ms-2 col text-center">
                                            <span type="button"
                                                onclick="copiar_otros(<?php echo isset($_GET['p']) ? Openssl::get_ssl_decrypt($_GET['p']) : ''; ?>)"
                                                class="btn btn-sm py-0 px-1 btn-outline-success waves-effect waves-light mb-2 ">Otros<i
                                                    class=" ri-file-copy-line"></i></span>
                                            <div style="max-height: 220px; min-height: 220px; overflow-y: scroll;border-radius: 5px;"
                                                class=" border border-success">
                                                <div class="text-center" id="cont_otro"></div>
                                            </div>
                                        </div>

                                        <div class="col-xl-5 bg-success" style="border:.1rem solid gray;margin-right: 2px; border-radius: 5px;">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center mt-1">
                                                    <span style="width: 4.5rem;" id="estadoProyecto" class="badge mx-1 text-primary bg-light"></span>
                                                    <span style="width: 3.5rem;" id="prioridad" class="badge mx-1" style="width: 3rem;"></span>
                                                    <span style="width: 10rem;" id="titulo_categoria"
                                                        class="badge bg-light text-dark mx-1"></span>
                                                    <span style="width: 10rem;" id="titulo_subCategoria"
                                                        class="badge bg-light text-dark mx-1"></span>
                                                </div>
                                            </div>


                                            <div style="display: flex; margin-top: .2rem; margin-bottom: .2rem;">
                                                <span class="badge bg-light text-dark mx-1">Ref: <span id="referencia_proy"></span></span>

                                                <span style="width: 7rem;"

                                                    class="me-2 badge bg-light text-primary ">Desde:
                                                    <span id="fech_inicio" class="text-dark"></span>
                                                </span>
                                                <span style="width: 7rem;"
                                                    class="me-2 badge bg-light text-primary ">Hasta:
                                                    <span id="fech_fin" class="text-dark"></span>
                                                </span>
                                                <span id="rechequeo" style="display: none; color:orangered" class="badge ml-1 bg-light"></span>
                                                <span id="cont_dimensionamiento" class="badge mx-1 text-primary bg-light fs-10">Horas: <span class="fw-bold" id="dimensionamiento"></span></span>
                                                <span id="proy_recurrencia" style="display: none; color:orangered" class="badge ml-1 bg-light"></span>

                                            </div>

                                            <div style="display: flex; margin-top: .2rem; margin-bottom: .2rem;">
                                                <span id="workshop" style="display: none;" class="badge mx-1 text-light bg-info border border-light">workshop</span>
                                            </div>

                                            <div style="display: flex; justify-content: end; align-items: center; margin-top: 0; margin-bottom: .2rem;">
                                                <span type="button" onclick="verLogs(<?php echo Openssl::get_ssl_decrypt($_GET['pg']) ?>)" style="background-color: #475569; display: inline-flex; align-items: center; gap: 4px;" class="badge border border-light">
                                                    Ver Logs <i class="ri-history-line fs-14"></i>
                                                </span>
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
                                                                    <div class="simplebar-content bg-dark"
                                                                        style="padding: 10px; border-radius: 5px;">
                                                                        <p style="font-size: .8rem; font-family:monospace; max-height: 160px;  min-height: 160px; overflow: hidden scroll;"
                                                                            class="text-light mb-2 px-1"
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
                                            class="badge bg-light text-success border border-success mx-2 mt-2 mb-3 d-inline-flex align-items-center gap-2 w-auto">Imagen</span>
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
                                            class="badge bg-light border border-success text-success mx-2 mt-2 mb-3 d-inline-flex align-items-center gap-2 w-auto">
                                            Documento
                                            <span title="Descargar el documento adjunto en el proyecto"
                                                id="documento_proy"></span>
                                        </span>
                                        <br>

                                        <div id="contHerramientas">

                                        </div>

                                    </div>
                                    <div class="col-xl-2 bg-success" style="border:.1rem solid gray;max-height: 500px;border-radius: 5px;">
                                        <div style="min-height: 250px;">
                                            <span style="font-size: .75rem; font-weight: 500;"
                                                class="badge bg-light text-dark mx-2 mt-2 mb-3 d-inline-flex align-items-center gap-1">
                                                Usuarios Asignados
                                                <i onclick="agregarUsuario(<?php echo Openssl::get_ssl_decrypt($_GET['p']) ?>)" title="Agregar nuevo usuario al proyecto" type="button" id="boton_agregar_usuarios_proy"
                                                    class="ri-user-add-line text-danger fw-bold fs-5"
                                                    style="cursor: pointer; display: none;"></i>
                                            </span>

                                            <ul class="bg-light"
                                                style="max-height: 12rem; border-radius: 5px; overflow-y: scroll; font-size: .8rem; font-weight: 500;"
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

                                        <section class="p-2 mt-2" style="display: none;" id="sect_descrip">
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
                                                    class="btn btn-sm btn-primary text-light" type="button" style="margin: 0 .1rem">
                                                    <span id="btn_texto_guardar">Guardar</span>
                                                    <span id="btn_spinner_guardar"
                                                        class="spinner-border spinner-border-sm ms-2 d-none" role="status"
                                                        aria-hidden="true"></span>
                                                </button>

                                                <button id="btn_finalizar_proyecto"
                                                    class="btn btn-sm btn-success text-light" style="margin: 0 .1rem">Finalizar</button>
                                            </section>
                                        </section>

                                        <hr>
                                        <hr>
                                        <div>
                                            <button id="btn_abrir_modal"
                                                class="btn btn-sm btn-info text-white"
                                                style="margin: 0 .5rem; padding: 3px 7px; letter-spacing: 0.3px; transition: all 0.2s ease;"
                                                onmouseover="this.style.transform='translateY(-.1px)'; this.style.boxShadow='0 4px 8px rgba(13,202,240,0.3)'; this.style.filter='brightness(1.15)';"
                                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.filter='brightness(1)';"
                                                onmousedown="this.style.transform='translateY(.1px)';"
                                                onmouseup="this.style.transform='translateY(-.1px)';">
                                                <i class="ri-folder-open-line me-1"></i> Visualizar Documentos
                                            </button>
                                        </div>
                                        <hr>
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
    header("Location:" . URL);
    exit;
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

        var habilitar_envio_correo = true; // ACA HABILITO CUANDO ME DEN LA CUENTA PARA ENVÍO DE EMAIL
        var validar_usu_asignado = false;

        document.addEventListener("DOMContentLoaded", function() {

            function mostrar_contenedores_de_activos(id_proyecto_gestionado) {
                $.ajax({
                    type: "POST",
                    url: "../../../../../Controller/ctrProyectos.php?proy=get_sector_x_proy",
                    data: {
                        id: id_proyecto_gestionado
                    },
                    dataType: "json",
                    success: function(response) {

                        switch (response.sector_id) {

                            case 1:
                                $("#contenedor_ips").show();
                                $("#contenedor_urls").show();
                                $("#contenedor_otros").show();
                                break;

                            case 2:
                                $("#contenedor_dispositivos").show();
                                $("#contenedor_agentes").show();
                                $("#contenedor_otros").show();
                                break;

                            case 3:
                                $("#contenedor_ips").show();
                                $("#contenedor_equipos").show();
                                $("#contenedor_otros").show();
                                break;

                            case 4:
                                $("#contenedor_ips_y_descripcion").text("NO POSEE HOSTS");
                                break;

                            case 5:
                                $("#contenedor_ips").show();
                                $("#contenedor_equipos").show();
                                $("#contenedor_otros").show();
                                $("#contenedor_dispositivos").show();
                                $("#contenedor_agentes").show();
                                $("#contenedor_urls").show();
                                break;

                            case 6:
                                $("#contenedor_ips").show();
                                $("#contenedor_urls").show();
                                $("#contenedor_otros").show();
                                break;
                        }
                        // 🔥 mostrar recién cuando el layout ya está correcto
                        $("#contenedor_ips_y_descripcion").css("opacity", "1");
                    }
                });
            }

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

                    data.forEach(elem => {

                        const estadoBloqueado = ["3", "4", "14", "15", "16", "17"].includes(String(elem.estados_id));

                        // Sector 4 siempre puede
                        if (sector_usu_id == "4") {
                            mostrar = true;
                            return;
                        }

                        // Estado cerrado bloquea todo excepto sector 4
                        if (estadoBloqueado) {
                            mostrar = false;

                            $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_usuario_finalizador_proyecto", {
                                id_proyecto_gestionado: id_proyecto_gestionado
                            }, function(data) {
                                document.getElementById("cont_usuario_finalizador").style.display = "block";
                                $("#cont_usuario_finalizador").html(data);
                            }, "html");

                            return;
                        }

                        // Usuario asignado puede escribir si el estado está abierto
                        if (elem.usu_asignado == session_usu_id) {
                            mostrar = true;
                            validar_usu_asignado = true;
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
                    $("#referencia_proy").text(data.refProy)
                    $("#dimensionamiento").text(data.dimensionamiento)
                    $("#estadoProyecto").text(data.estado)
                    switch (data.estado) {
                        case 'BORRADOR':
                            $("#estadoProyecto").addClass('badge mx-1 text-dark bg-light')
                            break;

                        case 'NUEVO':
                            $("#estadoProyecto").addClass('badge mx-1 fw-bold text-info border border-info bg-light')
                            break;

                        case 'ABIERTO':
                            $("#estadoProyecto").addClass('badge mx-1 fw-bold text-info border border-info bg-light')
                            break;

                        case 'REALIZADO':
                            $("#estadoProyecto").addClass('badge mx-1 fw-bold text-success border border-success bg-light')
                            break;

                        case 'CERRADO CALIDAD':
                            $("#estadoProyecto").addClass('badge mx-1 fw-bold text-success border border-success bg-light')
                            break;

                        case 'FIN SIN IMPLEM':
                            $("#estadoProyecto").addClass('badge mx-1 text-dark bg-light')
                            break;

                        case 'ELIMINADO':
                            $("#estadoProyecto").addClass('badge mx-1 text-danger bg-light border border-danger')
                            break;

                        case 'CANCELADO':
                            $("#estadoProyecto").addClass('badge mx-1 text-danger bg-light border border-danger')
                            break;

                        default:
                            break;
                    }

                    if (data.estado == 'REALIZADO' || data.estado == 'CERRADO CALIDAD')

                        $.post("../../../../../Controller/ctrProyectos.php?proy=validarContenedorBtnDockerfile", {
                                id: id_proyecto_gestionado,
                                pg: pg
                            },
                            function(data, textStatus, jqXHR) {
                                $("#contHerramientas").html(data);
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
                        if (data.tipo_rechequeo != null && data.tipo_rechequeo != "null" && data.tipo_rechequeo != "") {
                            $("#rechequeo").text("Retest " + data.tipo_rechequeo);
                        } else {
                            $("#rechequeo").text("Retest");
                        }
                    } else {
                        document.getElementById("rechequeo").style.display = "none";
                    }

                    $("#prioridad")
                        .removeClass("border-success border-warning border-danger")
                        .text("");
                    switch (data.prioridad_id) {
                        case 1:
                            $("#prioridad").addClass("bg-light   text-success").text(
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
                        subCatNom.substring(0, 30) + "..." :
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
                                            <i class=" ri-file-download-line text-success" style="font-size:1.3rem"></i>
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
                    let CatTruncado = CatNom.length > 30 ?
                        CatNom.substring(0, 30) + ".." :
                        CatNom;
                    $("#titulo_categoria")
                        .text(CatTruncado)
                        .attr("title", CatNom);

                    $("#titulo_servicio").text(data.titulo);
                    $("#parrafo_descripcion_proy").text("NOTA: " + data.descripcion);
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

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_equipo", {
                    id_proyecto_gestionado: id_proyecto_gestionado
                },
                function(data) {
                    if (data) {
                        $("#cont_equipos").html(data)
                    } else {
                        $("#cont_equipos").text("No hay equipos")
                    }
                }, "html");

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_agente", {
                    id_proyecto_gestionado: id_proyecto_gestionado
                },
                function(data) {
                    if (data) {
                        $("#cont_agentes").html(data)
                    } else {
                        $("#cont_agentes").text("No hay agentes")
                    }
                }, "html");

            $.post("../../../../../Controller/ctrProyectos.php?proy=get_hosts_proy_dispositivo", {
                    id_proyecto_gestionado: id_proyecto_gestionado
                },
                function(data) {
                    if (data) {
                        $("#cont_dispositivos").html(data)
                    } else {
                        $("#cont_dispositivos").text("No hay dispositivos")
                    }
                }, "html");

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
                            e.preventDefault();
                            let clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
                            let pastedHtml = clipboardData.getData('text/html');
                            let textoPlano = clipboardData.getData('text/plain');

                            if (pastedHtml) {
                                let tmp = document.createElement('div');
                                tmp.innerHTML = pastedHtml;

                                // Eliminar tags de Outlook
                                tmp.querySelectorAll('style, meta, link, o\\:p, w\\:sdt').forEach(el => el.remove());

                                // Eliminar atributos de formato
                                tmp.querySelectorAll('*').forEach(function(el) {
                                    el.removeAttribute('style');
                                    el.removeAttribute('class');
                                    el.removeAttribute('lang');
                                    el.removeAttribute('align');
                                    el.removeAttribute('valign');
                                    el.removeAttribute('bgcolor');
                                    el.removeAttribute('color');
                                    el.removeAttribute('face');
                                    el.removeAttribute('size');
                                });

                                // Reemplazar tablas (firmas de Outlook) por texto plano en un párrafo
                                tmp.querySelectorAll('table').forEach(function(table) {
                                    let texto = table.innerText.trim();
                                    if (texto) {
                                        let p = document.createElement('p');
                                        p.textContent = texto;
                                        table.replaceWith(p);
                                    } else {
                                        table.remove();
                                    }
                                });

                                // Eliminar p y div vacíos o con solo espacios/nbsp
                                tmp.querySelectorAll('p, div, span').forEach(function(el) {
                                    let contenido = el.innerHTML.replace(/&nbsp;/gi, '').trim();
                                    if (contenido === '') el.remove();
                                });

                                // Colapsar múltiples <br> consecutivos en uno solo
                                tmp.innerHTML = tmp.innerHTML
                                    .replace(/(&nbsp;|\s)+/g, ' ')
                                    .replace(/(<br\s*\/?>\s*){2,}/gi, '<br>');

                                document.execCommand('insertHTML', false, tmp.innerHTML);
                            } else {
                                document.execCommand('insertText', false, textoPlano);
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

                // Activar siempre el spinner y "Guardando"
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

                    // IMPORTANTE: Restaurar el botón si no hay descripción
                    spinner.classList.add("d-none");
                    textoBtn.textContent = "Guardar";
                    btn.disabled = false;
                    return;
                }

                if (validar_usu_asignado || sector_usu_id == 4) {
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

                            // Caso: archivos inválidos pero nota guardada
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Error!",
                        text: "No tiene permisos para realizar esta accion",
                        timer: 1100,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            });

            function enviarCorreoFinalizacion(id_proyecto_gestionado) {
                if (habilitar_envio_correo == true) {
                    return $.post(
                        URL + "/Controller/ctrCorreo.php?correo=enviar", {
                            id: id_proyecto_gestionado
                        }
                    );
                } else {
                    return false;
                }

            }

            function swalEnviandoCorreo() {
                if (habilitar_envio_correo == true) {
                    Swal.fire({
                        title: 'Finalizando proyecto',
                        html: 'Enviando correo de notificación a Calidad...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                } else {
                    return false;
                }

            }

            function finalizarProyectoAjax(id) {
                $.post(
                    "../../../../../Controller/ctrProyectos.php?proy=finalizar_proyecto", {
                        id_proyecto_gestionado: id,
                        estados_id: 3
                    }
                );

                $.post(
                    "../../../../../Controller/ctrProyectos.php?proy=finalizar_proyecto_tabla_estados_proyecto", {
                        id_proyecto_gestionado: id,
                        estados_id: 3
                    }
                );

                $.post(
                    "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                        id_proyecto_gestionado: id,
                        estados_id: 3
                    }
                );
            }

            function finalizar_proyecto(id_proyecto_gestionado) {
                btn_finalizar_proyecto.addEventListener("click", function() {
                    Swal.fire({
                        icon: 'info',
                        title: "¿Desea finalizar este Proyecto?",
                        text: 'Recuerde que una vez finalizado se le habilitará a Calidad el envío de los Informes al cliente',
                        showCancelButton: true,
                        confirmButtonText: 'OK'
                    }).then((result) => {

                        if (!result.isConfirmed) return;

                        $.post("../../../../../Controller/ctrProyectos.php?proy=update_proyecto_DesarrolloTasking", {
                            id_proyecto_gestionado
                        });

                        $.post("../../../../../Controller/ctrProyectos.php?proy=get_datos_proyecto_gestionado", {
                            id: id_proyecto_gestionado
                        }, function(data) {
                            const continuar = () => {
                                finalizarProyectoAjax(id_proyecto_gestionado);
                                if (habilitar_envio_correo == true) {
                                    swalEnviandoCorreo();
                                    enviarCorreoFinalizacion(id_proyecto_gestionado)
                                        .done(function(resp) {
                                            if (typeof resp === 'string') {
                                                resp = JSON.parse(resp);
                                            }
                                            if (resp.status === 'OK') {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Proyecto finalizado',
                                                    text: 'Proyecto cerrado y correo enviado correctamente',
                                                    timer: 1600,
                                                    showConfirmButton: false
                                                });
                                                setTimeout(() => location.reload(), 1600);
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Correo no enviado',
                                                    html: `<p>El proyecto fue finalizado correctamente, pero no se pudo enviar el correo.</p>`,
                                                    confirmButtonText: 'Ok'
                                                }).then(() => location.reload());
                                                console.log(resp.error);
                                            }
                                        })
                                        .fail(function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error inesperado',
                                                text: 'No se pudo contactar al servidor de correo'
                                            });
                                        });
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Bien',
                                        text: 'Proyecto finalizado correctamente',
                                        timer: 1600,
                                        showConfirmButton: false
                                    });
                                    setTimeout(() => location.reload(), 1600);
                                }

                            };
                            if (!data.fech_fin) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'No posee fecha fin',
                                    text: '¿Asignar fecha de hoy?',
                                    showCancelButton: true
                                }).then(res => {
                                    if (!res.isConfirmed) return;
                                    const today = new Date();
                                    const fechaHoy =
                                        today.getFullYear() + '-' +
                                        String(today.getMonth() + 1).padStart(2, '0') + '-' +
                                        String(today.getDate()).padStart(2, '0');
                                    $.post(
                                        "../../../../../Controller/ctrProyectos.php?proy=asignar_fecha_proyecto_finalizado_sin_fecha_fin", {
                                            id: data.id,
                                            fech_fin: fechaHoy
                                        },
                                        (resp) => resp.Status === "OK" && continuar(),
                                        "json"
                                    );
                                });
                            } else {
                                continuar();
                            }

                        }, "json");

                    });
                });
            }
            mostrar_contenedores_de_activos(id_proyecto_gestionado)
            finalizar_proyecto(id_proyecto_gestionado);


            const btnAbrirModal = document.getElementById("btn_abrir_modal");
            if (btnAbrirModal) {
                btnAbrirModal.addEventListener("click", function() {
                    $("#ModalEnviarCorreoCliente").modal("show");
                });
            }

            const btnEnviar = document.getElementById("btn_enviar_correo_cliente");
            if (btnEnviar) {
                btnEnviar.addEventListener("click", function() {

                    const inputCorreo = document.getElementById("correo_envio_email");
                    if (!inputCorreo) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se encontró el campo de correo'
                        });
                        return;
                    }

                    const correo = inputCorreo.value.trim();
                    if (!correo) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: 'Ingrese un correo destino',
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return;
                    }

                    const textoOriginal = btnEnviar.textContent;
                    btnEnviar.textContent = "Enviando...";
                    btnEnviar.disabled = true;

                    $.post("../../../../../Controller/ctrCorreo.php?correo=enviar_correo_cliente", {
                        id_proyecto_gestionado: id_proyecto_gestionado,
                        correo_destino: correo
                    }, function(data) {
                        console.log('Respuesta:', data);

                        // Siempre restaurar el botón
                        btnEnviar.textContent = textoOriginal;
                        btnEnviar.disabled = false;

                        if (data.status === 'OK_TEST') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ZIP generado OK',
                                html: `
                                <p><strong>Archivos comprimidos:</strong> ${data.archivos_encontrados}</p>
                                <p><strong>Clave:</strong> <code style="font-size:1.1rem; background:#eee; padding:2px 6px;">${data.clave}</code></p>
                                <p><strong>Ruta ZIP:</strong> <small>${data.zip}</small></p>
                                `,
                                showConfirmButton: true
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error || JSON.stringify(data)
                            });
                        }
                    }, 'json');
                });
            }
        });

        let correo_actualizado = false;

        $('#ModalEnviarCorreoCliente').on('hidden.bs.modal', function() {
            if (correo_actualizado) {
                location.reload();
            }
        });

        function reenviar_correo(id) {
            Swal.fire({
                icon: 'info',
                title: 'Atencion',
                text: 'Al confirmar, se registrará que el informe fue enviado al cliente por un medio alternativo. ¿Desea continuar?',
                showConfirmButton: true,
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("../../../../../Controller/ctrCorreo.php?correo=update_envio_correo", {
                            id: id,
                            status_envio: 'OK'
                        },
                        function(data, textStatus, jqXHR) {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Bien',
                                    text: 'Correo actualizado correctamente.',
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    timer: 1000
                                }).then(() => {
                                    $(`.badge_status_${id}`)
                                        .removeClass('bg-danger')
                                        .addClass('bg-success')
                                        .text('OK');

                                    $(`#correo_item_${id} .ri-mail-send-line`).closest('span').remove();

                                    // Agregar badge de confirmación
                                    $(`#correo_item_${id}`).append(`
                                <br><span class="badge bg-success text-light">
                                    <i class="ri-mail-check-line"></i> Correo enviado por otro medio
                                </span>
                                <br><span class="text-muted fs-11">Confirmado ahora</span>
                            `);

                                    correo_actualizado = true;
                                });
                            }
                        },
                        "json"
                    );
                    $.post(
                        "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                            id_proyecto_gestionado: id_proyecto_gestionado,
                            estados_id: 22
                        }
                    );
                }
            });
        }


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

            $.post(
                "../../../../../Controller/ctrAuditoria.php?case=insert_audit_estados_proyecto", {
                    id_proyecto_gestionado: id_proyecto_gestionado,
                    estados_id: 20
                }
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

        function copiar_dispositivos(id_proyecto_cantidad_servicios) {
            Toastify({
                text: "Dispositivos copiadas!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#0ab39c",
            }).showToast();

            let contenido = document.getElementById("cont_dispositivos").innerText.trim();
            navigator.clipboard.writeText(contenido).then(function() {
                toast.success('Successfully toasted!')
            }).catch(function(error) {
                console.error("Error al copiar: ", error);
            });
        }

        function copiar_agentes(id_proyecto_cantidad_servicios) {
            Toastify({
                text: "Agentes copiados!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#0ab39c",
            }).showToast();

            let contenido = document.getElementById("cont_agentes").innerText.trim();
            navigator.clipboard.writeText(contenido).then(function() {
                toast.success('Successfully toasted!')
            }).catch(function(error) {
                console.error("Error al copiar: ", error);
            });
        }

        function copiar_equipos(id_proyecto_cantidad_servicios) {
            Toastify({
                text: "Equipos copiados!",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#0ab39c",
            }).showToast();

            let contenido = document.getElementById("cont_equipos").innerText.trim();
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

        function verLogs(id_proyecto_gestionado) {
            $("#ModalVerLogsProyectos").modal("show")
            tabla = $("#tablaAuditoriaProyectosPorId").dataTable({
                "ajax": {
                    url: URL + "Controller/ctrAuditoria.php?case=get_auditoria_proyectos_x_id",
                    data: {
                        id: id_proyecto_gestionado
                    },
                    type: "post",
                    dataType: "json",
                    error: function(e) {}
                },
                "order": [
                    [7, "asc"]
                ],
                "bDestroy": true,
                "responsive": false,
                "bInfo": true,
                "iDisplayLength": 10,
                "autoWidth": false,
                "columnDefs": [{
                        "className": "text-center",
                        "targets": "_all"
                    },
                    {
                        "targets": 0,
                        "width": "3%"
                    },
                    {
                        "targets": 1,
                        "width": "25%"
                    },
                    {
                        "targets": 2,
                        "width": "5%"
                    },
                    {
                        "targets": 3,
                        "width": "10%"
                    },
                    {
                        "targets": 4,
                        "width": "10%"
                    },
                    {
                        "targets": 5,
                        "width": "20%"
                    },
                    {
                        "targets": 6,
                        "width": "25%"
                    },
                    {
                        "targets": 7,
                        "width": "1%"
                    }
                ],
                "language": {
                    "sProcessing": "Procesando..",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados..",
                    "sEmptyTable": "Ningun registro disponible en esta tabla",
                    "sInfo": "Mostrando un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando un total de 0 registros",
                    "sInfoFiltered": "(Filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar: ",
                    "sLoadingRecords": "Cargando",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    }
                }
            });
        }
    </script>