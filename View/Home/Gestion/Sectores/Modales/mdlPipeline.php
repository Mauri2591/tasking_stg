<!-- Modal -->
<div class="modal fade" id="ModalVerTecnologiasPipeline" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Pipeline</h5>
            </div>
            <div class="modal-body">
                <form id="formPipelineHerramienta" action="<?php echo URL . "/Controller/ctrProyectos.php?proy=descargarPipeline" ?>" method="post">

                    <input type="hidden" hidden id="id_proyecto_gestionado_pipeline" name="id_proyecto_gestionado">
                    <input type="hidden" hidden id="refProy_pipeline" name="refProy">
                    <input type="hidden" hidden id="client_rs_pipeline" name="client_rs">

                    <div class="col-lg-12">
                        <select name="comboHerramienta" class="form-select form-select-sm fw-bold" aria-label=".form-select-sm example">
                            <option class="text-center fw-bold" value="SEMBREP">Semgrep</option>
                        </select>
                    </div>

                    <div class="modal-footer mt-4 mb-0 pb-0">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                        <button name="btnDescargarPipeline" type="submit" class="btn btn-sm btn-primary">Descargar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>