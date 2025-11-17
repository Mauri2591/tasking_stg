<!-- Modal -->
<div class="modal fade" id="mdlReportesDocx" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo URL . "/Controller/ctrReportes.php?case=getDatosReporteSinFiltro" ?>" method="post">

                <div class="modal-header">
                    <h4>Descargar Reporte</h4>

                    <input type="hidden" hidden id="hiddenDocx" value="Docx">
                    <input type="hidden" hidden name="hiddenIdClienteDocx" id="hiddenIdClienteDocx">

                    <span id="fechaSeleccionadaEdit" class="badge fs-11 text-light border bg-primary"></span>
                </div>

                <div class="modal-body">
                    <section class="card-body border border-ligth p-2 mt-2 mb-4">
                        <div style="display: flex; justify-content: space-evenly;">
                            <div class="mb-2 row">
                                <label for="hora_desde" class="col-sm-3 col-form-label"> Desde</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="hora_desde_edit" id="hora_desde_edit">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label for="hora_hasta" class="col-sm-3 col-form-label">Hasta</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="hora_hasta_edit" id="hora_hasta_edit">
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <div class="input-group input-group-sm mt-1 align-items-center">
                                <div class="form-check form-switch me-2">
                                    <input class="form-check-input" value="NO" type="checkbox" id="idCheckValidarClienteDocx">

                                </div>
                                <span class="input-group-text flex-shrink-0" id="inputGroup-sizing-sm">
                                    Cliente:
                                </span>

                                <input id="nombreClienteDocx" disabled name="nombreClienteDocx" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-sm-12">
                                <input id="getNombreClienteDocx" readonly class="form-control form-control-sm" type="text">
                            </div>
                        </div>

                    </section>
                    <span class="text-danger"><Strong>Nota:</Strong> Puede descargar un reporte sin filtros y traer de todos los clientes toda la info, o sino aplicar filtro por fecha y/o Cliente</span>

                </div>
                <div class="modal-footer mt-5">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="generarReporteDocx" value="Consultar" class="btn btn-sm btn-primary">Descargar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="mdlReportesXlsx" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo URL . "/Controller/ctrReportes.php?case=getDatosReporteSinFiltro" ?>" method="post">
                <div class="modal-header">
                    <h4>Descargar Reporte</h4>

                    <input type="hidden" hidden id="hiddenXlsx" value="Xlsx">
                    <input type="hidden" hidden name="hiddenIdClienteXlsx" id="hiddenIdClienteXlsx">

                    <span id="fechaSeleccionadaEdit" class="badge fs-11 text-light border bg-primary"></span>
                </div>
                <div class="modal-body">
                    <section class="card-body border border-ligth p-2 mt-2 mb-4">
                        <div style="display: flex; justify-content: space-evenly;">
                            <div class="mb-2 row">
                                <label for="hora_desde" class="col-sm-3 col-form-label"> Desde</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="hora_desde_edit" id="hora_desde_edit">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label for="hora_hasta" class="col-sm-3 col-form-label">Hasta</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="hora_hasta_edit" id="hora_hasta_edit">
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <div class="input-group input-group-sm mt-1 align-items-center">
                                <div class="form-check form-switch me-2">
                                    <input class="form-check-input" value="NO" type="checkbox" id="idCheckValidarClienteXlsx">

                                </div>
                                <span class="input-group-text flex-shrink-0" id="inputGroup-sizing-sm">
                                    Cliente:
                                </span>

                                <input id="nombreClienteXlsx" disabled name="nombreClienteXlsx" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-sm-12">
                                <input id="getNombreClienteXlsx" readonly class="form-control form-control-sm" type="text">
                            </div>
                        </div>

                    </section>
                    <span class="text-danger"><Strong>Nota:</Strong> Puede descargar un reporte sin filtros y traer de todos los clientes toda la info, o sino aplicar filtro por fecha y/o Cliente</span>

                </div>
                <div class="modal-footer mt-5">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="generarReporteXlsx" value="Consultar" class="btn btn-sm btn-primary">Descargar</button>
                </div>
            </form>
        </div>
    </div>
</div>