<!-- Modal -->
<div class="modal fade" id="ModalDescargarPdfProyectos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-ls">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportar Log de Proyectos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-xl-12">
                    <div class="card crm-widget">
                        <div class="card-body p-0">
                            <form action="<?= URL ?>Controller/ctrReportes.php" method="GET" target="_blank">
                                <input type="hidden" name="case" value="get_audit_proyectos_x_fecha">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Desde</label>
                                        <input type="date" name="desde" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Hasta</label>
                                        <input type="date" name="hasta" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="mt-4 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="ri-file-pdf-line me-1"></i> Descargar PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>