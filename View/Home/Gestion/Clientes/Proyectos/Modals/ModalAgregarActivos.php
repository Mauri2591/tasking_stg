<!-- Modal -->
<div class="modal fade" id="ModalAgregarActivos" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-light">
            <div class="card-body">
                <div class="row mt-2 p-6">
                    <div class="col-sm-12">
                        <form id="agregar_activos_borrador" method="post">
                            <select id="combo_select_activo" class="form-select form-select-sm"
                                aria-label=".form-select-sm example">
                                <option value="IP">IP</option>
                                <option value="URL">URL</option>
                                <option value="OTRO">OTRO</option>
                            </select>
                            <input id="agregar_nuevo_host" class="form-control form-control-sm mt-2" type="text">
                        </form>
                        <div id="mje_host_agregar">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mb-2">
                <button id="btn_agregar_nuevos_hosts_borrador" type="submit"
                    class="btn btn-sm btn-primary">Guardar</button>
                <button type="button" id="btn_cancelar_proyecto" class="btn btn-sm btn-light"
                    data-bs-dismiss="modal">Cerrar</button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>