<!-- Modal Descargar Excel -->
<div class="modal fade" id="modalDescargarExcelProyectosTotal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formDescargarReporteXlsx" action="<?php echo URL . "Controller/ctrReportes.php?case=total_excel" ?>" method="post">
                <div class="card-body">
                    <h5 class="mb-2 pb-0">Descargar Archivo <i class="ri-file-excel-2-fill text-success fs-22" title="Descargar documento"></i></h5>

                    <section class="card-body border border-light p-2 mt-4 mb-4">
                        <div style="display: flex; justify-content: space-evenly;">
                            <div class="mb-2 row">
                                <label class="col-sm-3 col-form-label">Desde</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="fecha_desde">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label class="col-sm-3 col-form-label">Hasta</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="fecha_hasta">
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="d-flex justify-content-end mt-5 mb-2 me-2">
                        <button type="button" class="btn btn-sm btn-light mx-2" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-primary">Descargar</button>
                    </div>
                </div>
            </form>

            <div class="mt-3 mb-1 mx-2 text-center p-2 border bg-light">
                <small class="text-danger mt-0 fs-13">
                    Este reporte muestra el <strong>TOTAL</strong> de todos los Proyectos de todos los Clientes.<br> Se excluyen aquellos que hayan sido <strong>Borrados o Finalizados Sin Implementar</strong>.
                </small>
            </div>
        </div>
    </div>
</div>


<!-- Modal delete proyectos caducados -->
<div class="modal fade" id="modalProyectosCaducados" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formProyectosCaducados" method="post">
                <div class="card-body">
                    <h5 class="mb-2 pb-0">Eliminar Documentación de Proyectos Caducados</h5>
                    <section class="card-body border border-light p-2 mt-4 mb-4">
                        <div class="row justify-content-center align-items-center">
                            <label for="anios_caducados" class="col-sm-6 col-form-label text-end">Antigüedad mayor a</label>
                            <div class="col-sm-3">
                                <select name="anios_caducados" id="anios_caducados" class="form-select form-select-sm" required>
                                    <option value="1">1 año</option>
                                    <option value="2">2 años</option>
                                    <option value="3">3 años</option>
                                    <option value="4">4 años</option>
                                    <option value="5">5 años</option>
                                    <option value="6">6 años</option>
                                    <option value="7">7 años</option>
                                    <option value="8">8 años</option>
                                    <option value="9">9 años</option>
                                    <option value="10">10 años</option>
                                </select>
                            </div>
                        </div>
                    </section>
                    <div class="d-flex justify-content-end mt-5 mb-2 me-2">
                        <button type="button" class="btn btn-sm btn-light mx-2" data-bs-dismiss="modal">Cerrar</button>
                        <button id="btn_eliminar_caducados" type="button" class="btn btn-sm btn-danger">Consultar</button>
                    </div>
                </div>
            </form>

            <div class="mt-3 mb-1 mx-2 text-center p-2 border bg-light">
                <small class="text-danger mt-0 fs-13">
                    Se eliminará la documentación de los proyectos cuya <strong>fecha de contrato</strong> (o de creación si no posee de Contrato) supere la antigüedad seleccionada.<br>
                    <strong>Esta acción no se puede deshacer.</strong>
                </small>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallePurga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de la purga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table id="table_detalle_purga" class="table table-bordered dt-responsive table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>PROYECTO</th>
                            <th>CARPETA</th>
                            <th>ZIP</th>
                            <th>CLAVE ZIP</th>
                            <th>ARCHIVO CALIDAD</th>
                            <th>CAPTURAS</th>
                            <th>HOSTS</th>
                            <th>VER</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>