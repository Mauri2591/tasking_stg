<!-- Modal -->
<div class="modal fade" id="modalDescargarExcelProyectosTotal" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formDescargarReporteXlsx" action="<?php echo URL . "Controller/ctrReportes.php?case=total_excel" ?>" method="post">
            <div class="modal-content">
                <div class="card-body">
                    <h5 style="height: 2rem;" class="mb-2 pb-0">Descargar Archivo <i class="ri-file-excel-2-fill text-success fs-22" title="Descargar documento"></i>
                        <div class="mt-4 live-preview">
                            <div style="display: flex; justify-content: end;">
                    </h5>
                    <section class="card-body border border-ligth p-2 mt-4 mb-4">
                        <div style="display: flex; justify-content: space-evenly;">
                            <div class="mb-2 row">
                                <label for="hora_desde" class="col-sm-3 col-form-label"> Desde</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="fecha_desde">
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <label for="hora_hasta" class="col-sm-3 col-form-label">Hasta</label>
                                <div class="col-sm-7 ml-2">
                                    <input type="date" class="form-control form-control-sm" name="fecha_hasta">
                                </div>
                            </div>

                        </div>
                    </section>

                    <div style="display: flex; justify-content: end; margin-bottom: .5rem; margin-top: 3rem; margin-right: .5rem;">
                        <button type="button" class="btn btn-sm btn-light mx-2"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-primary">Descargar</button>
                    </div>
        </form>
        <div class="mt-3 mb-1 mx-2 text-center p-2 border bg-light">
            <small class="text-danger mt-0 fs-12">
                Este reporte muestra el total de todos los <strong>Proyectos por Cliente.</strong><br> Se excluyen aquellos que hayan sido <strong>Borrados o Finalizados Sin Implementar</strong>.
            </small>
        </div>
    </div>
</div>
</div>