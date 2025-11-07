<!-- Modal -->
<div class="modal fade" id="ModalConsultarActivos" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-body">
                <span id="proy_cliente_periodo" class="fw-bold"></span><br>

                <span class="mt-4 badge bg-light border border-primary fs-10 text-primary"
                    style="font-weight: bold;">Activos</span><span onclick='agregar_activos_borrdor()'
                    data-placement="top" title="Agregar nuevos" type="button"><i
                        class=" ri-file-add-fill text-success fs-20"></i></span>
                <div class="row mt-2 p-6">
                    <div class="col-sm-12" style="height: 500px; overflow-y: scroll;">
                        <table id="table_container_activos_proy_creado" class="p-0 m-0">
                            <thead>
                                <tr>
                                    <th style="width: 20%; text-align: center;">Tipo</th>
                                    <th style="width: 70%; text-align: center;">Host</th>
                                    <th style="width: 70%; text-align: center;">Estado</th>
                                    <th style="width: 10%; text-align: center;">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-end mb-2" style="margin-right: 20px;">
                <button type="button" id="btn_cancelar_proyecto" class="btn btn-sm btn-light"
                    data-bs-dismiss="modal">Cerrar</button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>