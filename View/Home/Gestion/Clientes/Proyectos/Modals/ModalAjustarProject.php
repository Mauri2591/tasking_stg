<!-- Modal -->
<div class="modal fade" id="ModalAltaProject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-body">
                <h5 class="mb-0 pb-0">Crear Proyecto</h5>
                <!-- <code>Los campos con (*) son obligatorios</code> -->
                <div class="mt-4 live-preview">
                    <form id="form_alta_proyecto" enctype="multipart/form-data">

                        <!-- <input type="text" id="id_proyecto_proyecto_cantidad_servicios">-->

                        <input type="hidden" hidden id="id_proyecto_cantidad_servicios">
                        <input type="hidden" hidden id="id_proyecto_gestionado">
                        <input type="hidden" hidden id="mdl_id_proyecto_gestionado">

                        <span class="mb-2 badge bg-light border border-primary fs-10 text-primary"
                            style="font-weight: bold;">Datos del
                            Cliente</span>
                        <div class="col-12">
                            <div class="row">
                                <div class="mb-3 col-sm-6">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Cliente</span>
                                    <input type="text" class="bg-primary text-light form-control form-control-sm"
                                        readonly id="client_rs_alta_proy">
                                </div>

                                <div class="mb-3 col-sm-2">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Pais</span>
                                    <input type="hidden" hidden id="id_pais_id_carga_proy">
                                    <input id="pais_id_carga_proy" type="text" disabled
                                        class="bg-primary text-light form-control form-control-sm"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>

                                <div class="mb-3 col-sm-2">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Creador</span>
                                    <input type="hidden" hidden id="usu_id_creador_proy_nuevo">
                                    <input type="text" disabled
                                        class="form-control bg-primary text-light form-control-sm"
                                        value="<?php echo $_SESSION['usu_nom'] ?>">
                                </div>

                                <div class="mb-3 col-sm-2">
                                    <span id="recurrencia" class="badge bg-light fs-10 mb-1 text-dark">Recurrencia</span>
                                    <select id="combo_recurrente_proy_nuevo" class="form-select form-select-sm"
                                        aria-label=".form-select-sm example">
                                        <option value="0">NO</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="13">13</option>
                                        <option value="14">14</option>
                                        <option value="15">15</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                        <option value="20">20</option>
                                        <option value="21">21</option>
                                        <option value="22">22</option>
                                        <option value="23">23</option>
                                        <option value="24">24</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-sm-6">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Titulo</span>
                                    <input readonly type="text"
                                        class="bg-primary text-light form-control form-control-sm"
                                        id="titulo_client_rs_alta_proy">
                                </div>

                                <div class="mb-3 col-sm-2">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Referencia</span>
                                    <input autofocus type="text" class="form-control form-control-sm"
                                        id="client_refPro_proy_nuevo">
                                </div>


                                <div class="mb-1 col-sm-2">
                                    <label class="badge bg-light fs-10 mb-1 text-dark d-flex align-items-center justify-content-center" style="white-space: nowrap;">
                                        <span class="text-danger fs-13">* </span> Horas
                                    </label>
                                    <input autofocus type="number" class="form-control form-control-sm" id="hs_dimensionadas">
                                </div>

                                <div class="mb-3 col-sm-2" id="cont_combo_workshop">
                                    <span id="recurrencia" class="badge bg-info fs-10 mb-1 text-light border">Workshop</span>
                                    <select id="combo_workshop" class="form-select form-select-sm fw-bold"
                                        aria-label=".form-select-sm example">
                                        <option value="NO">NO</option>
                                        <option value="SI">SI</option>
                                    </select>
                                </div>

                            </div>

                        </div>

                        <span class="mt-2 mb-2 badge bg-light border border-primary fs-10 text-primary"
                            style="font-weight: bold;">Datos del
                            Servicio
                        </span>

                        <div class="col-12">
                            <div class="row">
                                <div class="mb-3 col-sm-3">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Sector</span>
                                    <select id="combo_sector_proy_nuevo" class="form-select form-select-sm"
                                        aria-label=".form-select-sm example">

                                    </select>
                                </div>

                                <div class="mb-3 col-sm-4">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Producto</span>
                                    <select id="combo_categoria_proy_nuevo" class="form-select form-select-sm"
                                        aria-label=".form-select-sm example">

                                    </select>
                                </div>
                                <div class="mb-3 col-sm-3">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Tipo</span>
                                    <select id="combo_subcategoria_proy_nuevo" class="form-select form-select-sm"
                                        aria-label=".form-select-sm example">

                                    </select>
                                </div>

                                <div class="mb-3 col-sm-2">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Prioridad</span>
                                    <select id="combo_prioridad_proy_nuevo"
                                        class="form-select border-success form-select-sm"
                                        aria-label=".form-select-sm example">

                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Usuarios</span>
                                    <div style="height: 100px; overflow-y: scroll;">
                                        <div class="form-check" id="combo_usuario_x_sector">

                                        </div>
                                    </div>

                                </div>

                                <div class="mb-3 col-sm-3">
                                    <span class="badge bg-light fs-10 mb-1 text-dark"><span class="text-danger fs-13">*
                                        </span>Fecha Inicio</span>
                                    <input class="form-control form-control-sm" id="fech_ini_proy_nuevo" type="date">
                                </div>

                                <div class="mb-3 col-sm-3">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Fecha Fin</span>
                                    <input class="form-control form-control-sm" id="fech_fin_proy_nuevo" type="date">
                                </div>

                                <div class="mb-3 col-sm-3">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Fecha Vantive</span>
                                    <input class="form-control form-control-sm" id="fech_vantive" type="date">
                                </div>
                            </div>

                            <div id="cont_archivo" class="mb-1 col-sm-12">
                                <span class="mt-4 mb-1 badge bg-light border border-primary fs-10 text-primary"
                                    style="font-weight: bold;">Documentos
                                </span>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <span class="badge bg-light fs-10 mb-1 text-dark">Archivo</span>
                                        <input class="form-control form-control-sm" name="archivo"
                                            accept=".jpg, .jpeg, .png, .doc, .docx, .txt, .pdf, .zip" id="archivo"
                                            type="file">
                                    </div>

                                    <div class="col-sm-6">
                                        <span class="badge bg-light fs-10 mb-1 text-dark">Captura de Imagen</span><br>
                                        <input type="text" id="captura_imagen" name="captura_imagen"
                                            class="form-control-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" id="cont_mje_proy_archivo">

                            </div>

                            <div id="cont_activos_ips_urls_otros">
                                <!-- Inicio ACTIVOS -->
                                <span class="mt-3 badge bg-light border border-primary fs-10 text-primary"
                                    style="font-weight: bold;">Ingrese
                                    activos</span>
                                <div id="container_ips_urls" class="row p-2">
                                    <div class="col-sm-3 mr-1">
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

                                            <textarea class="form-control" id="otros_proy_nuevo_eh" rows="3"
                                                placeholder="Otros"></textarea>
                                        </div>
                                        <div id="mje_urls_proy_nuevo_otros">
                                        </div>
                                    </div>
                                </div>
                                <!-- Fin ACTIVOS -->
                            </div>

                            <div class="col-sm-12" id="cont_descripcion_proy">
                                <span class="mt-2 mb-2 badge bg-light border border-primary fs-10 text-primary"
                                    style="font-weight: bold;">Descripcion</span><br>
                                <textarea class="form-control" id="descripcion_proy" rows="3"></textarea>
                            </div>

                            <p id="contenedor_validad_proy_Desa_interno_tasking" class="text-danger my-2"><strong>Atención: <br></strong> Los proyectos de Desarrollo de Tasking podrán cargarse con una periodicidad mínima de dos (2) meses. Una vez finalizado el proyecto en curso y cumplido dicho período, se podrá generar un nuevo proyecto</p>

                            <section id="contenedor_cont_activos">
                                <div id="cont_activos" class="mt-2">
                                    <span class="mt-3 badge bg-light border border-primary text-primary"
                                        style="font-weight: bold;">Activos</span><span
                                        onclick='consultar_activos_borrdor($("#valor_cantidad_servicios").val())'
                                        data-placement="top" title="Consultar activos" type="button"><i id="icon_activos"
                                            class=" ri-file-add-fill text-success fs-20"></i>
                                    </span>
                                </div>
                            </section>
                        </div>
                        <hr>
                        <br>
                        <div id="cont_btn_Modal_Ajustar_proy" class="text-end mt-3">
                            <button id="btn_crear_proyecto" type="submit" class="btn btn-sm btn-primary">Crear</button>

                            <button id="btn_eliminar_proyecto"
                                class="btn btn-sm btn-danger">Eliminar</button>

                            <button id="btn_finalizar_estado_proyecto" type="submit" class="btn btn-sm"
                                style="background-color: gray; color:#fff">Fin sin impl</button>

                            <button id="btn_editar_proyecto" type="button" class="btn btn-sm btn-success">
                                Guardar Edición
                            </button>

                            <button id="btn_cambiar_estado_proyecto" type="submit"
                                class="btn btn-sm btn-primary">Cambiar
                                a Nuevo</button>

                            <button type="button" id="btn_cancelar_proyecto" class="btn btn-sm btn-light"
                                data-bs-dismiss="modal">Cerrar</button>

                        </div>
                        <div id="cont_mje_proy_crear">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>