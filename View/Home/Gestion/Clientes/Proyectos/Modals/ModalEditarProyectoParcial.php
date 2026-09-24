<div class="modal fade" id="ModalEditarProyectoParcial" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-body">
                <h5 class="mb-2 pb-0">Edicion Parcial del Proyecto</h5>
                <div class="card card-body">
                    <form id="formProyecto">
                        <input type="hidden" hidden id="sector_id_update_parcial" name="sector_id_update_parcial">
                        <input type="hidden" hidden id="id_proyecto_gestionadoa_update_parcial">

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="titulo" class="form-label mb-0 fs-12">Título</label>
                                <input autocomplete="off" type="text" class="form-control form-control-sm" id="titulo" name="titulo">
                            </div>
                            <div class="col-md-2">
                                <label for="referencia" class="form-label mb-0 fs-12">Referencia</label>
                                <input autocomplete="off" type="text" class="form-control form-control-sm" id="referencia" name="referencia">
                            </div>
                            <div class="col-md-2">
                                <label for="tipo" class="form-label mb-0 fs-12">Dimensionamiento</label>
                                <input autocomplete="off" type="text" class="form-control form-control-sm" id="dimensionamiento_update_parcial" name="dimensionamiento_update_parcial">
                            </div>
                            <div class="col-md-2">
                                <label for="tipo" class="form-label mb-0 fs-12">Tipo</label>
                                <select class="form-select form-select-sm" id="tipo" name="tipo"></select>
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_vantive" class="form-label mb-0 fs-12">Fecha Vantive</label>
                                <input autocomplete="off" type="date" class="form-control form-control-sm" id="fecha_vantive" name="fecha_vantive">
                            </div>
                            <div class="col-md-4">
                                <label for="inicio" class="form-label mb-0 fs-12">Inicio</label>
                                <input autocomplete="off" type="date" class="form-control form-control-sm" id="inicio" name="inicio">
                            </div>
                            <div class="col-md-4">
                                <label for="fin" class="form-label mb-0 fs-12">Fin</label>
                                <input autocomplete="off" type="date" class="form-control form-control-sm" id="fin" name="fin">
                            </div>

                            <div class="col-md-6">
                                <label for="correo_envio_cliente" class="form-label mb-0 fs-12">Correo cliente</label>
                                <textarea class="form-control form-control-sm" id="correo_envio_cliente_edicion_parcial" name="correo_envio_cliente_edicion_parcial" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="correo_envio_cliente_copias" class="form-label mb-0 fs-12">Correos en copia</label>
                                <textarea class="form-control form-control-sm" id="correo_envio_cliente_copias_edicion_parcial" name="correo_envio_cliente_copias_edicion_parcial" rows="2"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label for="descripcion" class="form-label mb-0 fs-12">Descripción</label>
                                <textarea class="form-control form-control-sm" id="descripcion" name="descripcion" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3 mx-2">
                            <button id="btn_update_parcial" class="btn btn-sm btn-success">Guardar</button>
                        </div>
                    </form>
                    <div style="display: none; justify-content: center;" id="mje_update_parcial">
                        <div class="alert alert-success mt-4 text-center" role="alert">
                            <strong>Bien</strong><br>
                            <p>Actualizado correctamente!</p>
                        </div>
                    </div>

                    <div style="display: none; justify-content: center;" id="mje_campos_vacios_update_parcial">
                        <div class="alert alert-warning mt-4 text-center" role="alert">
                            <strong>Error</strong><br>
                            <p>Hay campos obligatorios vacíos!</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>