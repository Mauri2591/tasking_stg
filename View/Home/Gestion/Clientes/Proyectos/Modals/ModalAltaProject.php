<!-- Modal -->
<div class="modal fade show" id="ModalCrearProject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card-body">
                <h5 class="mb-0 pb-0">Crear Proyecto</h5>
                <!-- <code>Los campos con (*) son obligatorios</code> -->
                <div class="mt-4 live-preview">
                    <form id="form_crear_proyecto" action="javascript:void(0);">

                        <input type="hidden" hidden id="client_id_hidden">
                        <input type="hidden" hidden id="pais_id_carga_proy_hidden">

                        <span class="mt-1 mb-2 badge bg-light border border-primary fs-10 text-primary"
                            style="font-weight: bold;">Datos del
                            Cliente</span>
                        <div class="col-12">
                            <div class="row">
                                <div class="mb-3 col-sm-9">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Cliente</span>
                                    <input type="text" class="bg-primary text-light form-control form-control-sm"
                                        readonly id="client_rs_alta_proy">
                                </div>

                                <div class="mb-3 col-sm-3">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Pais</span>
                                    <input id="pais_id_carga_proy" type="text" disabled
                                        class="bg-primary text-light form-control form-control-sm"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="mb-3 col-sm-8">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Creador</span>
                                    <input type="hidden" hidden id="usu_id_creador_proy_nuevo">
                                    <input type="text" disabled
                                        class="form-control bg-primary text-light form-control-sm"
                                        value="<?php echo $_SESSION['usu_nom'] ?>">
                                </div>

                                <div class="mb-3 col-sm-4">
                                    <span class="badge bg-light fs-10 mb-1 text-dark">Cantidad de servicios</span>
                                    <select id="combo_cantidad_servicios_proy_nuevo"
                                        name="combo_cantidad_servicios_proy_nuevo" class="form-select form-select-sm"
                                        style="font-weight: bold;" aria-label=".form-select-sm example">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button id="btn_crear_proyecto" type="submit" class="btn btn-sm btn-primary">Crear</button>
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