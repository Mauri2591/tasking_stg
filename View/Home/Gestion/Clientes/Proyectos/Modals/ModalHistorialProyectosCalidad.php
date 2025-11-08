<!-- Modal -->
<div class="modal fade show" id="ModalHistorialProyectosCalidad" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-body">
                <span class="badge bg-primary text-light fs-12">Cliente: <span id="idCliente" style="text-transform: uppercase;"></span></span>
                <input type="hidden" hidden id="inputHiddenIdCliente">
                <span><i onclick="descargarExcel(document.getElementById('inputHiddenIdCliente').value)" class="ri-file-excel-2-fill text-success fs-22" type="button" title="Descargar documento"></i></span>
                <div class="card card-body">
                    <table id="tablelHistorialProyectosCalidad" style="text-align: center; width: 100%;">
                        <thead style="text-align: center;">
                            <tr style="text-align: center;">
                                <th style="width: 5%;text-align: center;">ID</th>
                                <th style="width: 45%;text-align: center;">TITULO</th>
                                <th style="width: 5%;text-align: center;">REC</th>
                                <th style="width: 5%;text-align: center;">RECH</th>
                                <th style="width: 5%;text-align: center;">REF</th>
                                <th style="width: 5%px;text-align: center;">CREACION</th>
                                <th style="width: 5%px;text-align: center;">SECTOR</th>
                                <th style="width: 5px;text-align: center;">PROD</th>
                                <th style="width: 5px;text-align: center;">HS</th>
                                <th style="width: 5px;text-align: center;">EST</th>
                                <th style="width: 5px;text-align: center;">AGREGAR <br>RECHEQUEO</th>
                                <th style="width: 5px;text-align: center;">INFO</th>
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
                                <td style="width: 5%;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>