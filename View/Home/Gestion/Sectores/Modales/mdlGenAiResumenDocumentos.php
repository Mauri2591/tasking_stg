<!-- Modal -->
<div class="modal fade" id="modalIaResumenDocumentos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <style>
        #tablaResumenDocumentos {
            table-layout: fixed;
        }

        #modalIaResumenDocumentos .modal-content,
        #modalIaResumenDocumentos .modal-header,
        #modalIaResumenDocumentos .modal-body,
        #modalIaResumenDocumentos .card {
            background-color: #232327 !important;
            border-color: #313244 !important;
        }

        #modalIaResumenDocumentos .modal-title {
            color: #f5f5f5 !important;
        }

        #tablaResumenDocumentos {
            --bs-table-bg: #1e1e2e;
            --bs-table-color: #f5f5f5;
            --bs-table-border-color: #313244;
            --bs-table-hover-bg: #1e1e2e;
            --bs-table-hover-color: #f5f5f5;
        }

        #tablaResumenDocumentos thead th {
            background-color: #232327 !important;
            color: #f5f5f5 !important;
            border-color: #313244 !important;
        }

        #tablaResumenDocumentos tbody td {
            border-color: #313244 !important;
            color: #f5f5f5 !important;
            opacity: 1 !important;
        }

        #tablaResumenDocumentos tbody tr:hover td {
            background-color: #0c0c0e !important;
            color: #f5f5f5 !important;
        }

        #tablaResumenDocumentos {
            font-size: 16px;
        }

        #tablaResumenDocumentos thead th {
            font-size: 14px;
        }
    </style>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resumen de Documentos con IA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-xl-12">
                    <div class="card crm-widget">
                        <div class="card-body p-0">
                            <table id="tablaResumenDocumentos" class="table w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Documento</th>
                                        <th style="width: 90%;">Resumen</th>
                                        <th style="width: 5px;"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>