<!-- Modal -->
<div class="modal fade show" id="ModalHistorialProyectosCalidad" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-body">
                <div class="p-2 border bg-light" style="margin-bottom: .5rem;">
                    <div style="display: flex; justify-content: end; align-items: center;">
                        <span class="badge bg-primary text-light">Reporte Cliente</span> <i onclick="descargarExcel(document.getElementById('inputHiddenIdCliente').value)" class="ri-file-excel-2-fill text-success fs-22" type="button" title="Descargar documento"></i>
                    </div>
                    <span class="badge bg-primary text-light fs-12">Cliente: <span id="idCliente" style="text-transform: uppercase;"></span></span>
                    <input type="hidden" hidden id="inputHiddenIdCliente">
                </div>

                <?php if ($_SESSION['sector_id'] == "4"): ?>

                    <div class="card card-body">
                        <table id="tablelHistorialProyectosCalidad" style="text-align: center; width: 100%;">
                            <thead style="text-align: center;">
                                <tr style="text-align: center;">
                                    <th style="width: 5%;text-align: center;">ID</th>
                                    <th style="width: 45%;text-align: center;">TITULO</th>
                                    <th style="width: 5%;text-align: center;">REC</th>
                                    <th style="width: 5%;text-align: center;">RECH</th>
                                    <th style="width: 5%;text-align: center;">REF</th>
                                    <th style="width: 5%px;text-align: center;">FECHA</th>
                                    <th style="width: 5%px;text-align: center;">SECTOR</th>
                                    <th style="width: 5px;text-align: center;">PROD</th>
                                    <th style="width: 5px;text-align: center;">HS</th>
                                    <th style="width: 5px;text-align: center;">EST</th>
                                    <th style="width: 5px;text-align: center;">AGREGAR <br>RECHEQUEO</th>
                                    <th style="width: 5px;text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody style="text-align: center;">
                                <tr style="text-align: center;">
                                    <td style="width: 5%;"></td>
                                    <td style="width: 45%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>

                    <div class="card card-body">
                        <table id="tablelHistorialProyectosCalidad" style="text-align: center; width: 100%;">
                            <thead style="text-align: center;">
                                <tr style="text-align: center;">
                                    <th style="width: 5%;text-align: center;">ID</th>
                                    <th style="width: 45%;text-align: center;">TITULO</th>
                                    <th style="width: 5%;text-align: center;">REC</th>
                                    <th style="width: 5%;text-align: center;">RECH</th>
                                    <th style="width: 5%;text-align: center;">REF</th>
                                    <th style="width: 5%px;text-align: center;">FECHA</th>
                                    <th style="width: 5%px;text-align: center;">SECTOR</th>
                                    <th style="width: 5px;text-align: center;">PROD</th>
                                    <th style="width: 5px;text-align: center;">HS</th>
                                    <th style="width: 5px;text-align: center;">EST</th>
                                    <th style="width: 5px;text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody style="text-align: center;">
                                <tr style="text-align: center;">
                                    <td style="width: 5%;"></td>
                                    <td style="width: 45%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 5%;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>