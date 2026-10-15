<div class="modal fade" id="mdlProyectosPreNuevos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills arrow-navtabs nav-success mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active py-1 px-2" id="tab-pre-nuevos-btn" data-bs-toggle="tab" href="#tab-pre-nuevos" role="tab" aria-selected="true">
                            <span class="d-block d-sm-none"><i class="mdi mdi-timer-sand"></i></span>
                            <span class="d-none d-sm-block">Pre-nuevos</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link py-1 px-2" id="tab-recurrentes-sector-btn" data-bs-toggle="tab" href="#tab-recurrentes-sector" role="tab" aria-selected="false">
                            <span class="d-block d-sm-none"><i class="mdi mdi-repeat"></i></span>
                            <span class="d-none d-sm-block">Recurrentes</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-pre-nuevos" role="tabpanel" aria-labelledby="tab-pre-nuevos-btn">
                        <table id="table-proyectos-pre-nuevos" class="table table-sm align-middle w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>TITULO</th>
                                    <th>INICIO</th>
                                    <th>FINAL</th>
                                    <th>RECURRENCIA</th>
                                    <th>RETEST</th>
                                    <th>PM</th>
                                    <th>PRODUCTO</th>
                                    <th>TIPO</th>
                                    <th>HS</th>
                                    <th>ASIGNADO</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="text-center"></tbody>
                        </table>

                        <p class="mt-4 fs-15 text-danger mx-2 text-center">
                            Estos son los proyectos se pueden pasar a estado <strong>Nuevo</strong> para comenzar a trabajarlos.
                        </p>
                    </div>

                    <div class="tab-pane fade" id="tab-recurrentes-sector" role="tabpanel" aria-labelledby="tab-recurrentes-sector-btn">
                        <table id="table-proyectos-recurrentes-sector" class="table table-sm align-middle w-100">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 400px;text-align: center;">CLIENTE</th>
                                    <th style="width: 20px;text-align: center;">PRODUCTO</th>
                                    <th style="width: 20px;text-align: center;">TOTAL</th>
                                    <th style="width: 20px;text-align: center;">UTILIZADAS</th>
                                </tr>
                            </thead>
                            <tbody class="text-center"></tbody>
                        </table>

                        <p class="mt-4 fs-15 text-danger mx-2 text-center">
                            Estos son los proyectos recurrentes. No se pueden gestionar, solo visualizar.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>