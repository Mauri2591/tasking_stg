<?php if ($_SESSION['sector_id'] == "4"): ?>
    <!-- Modal -->
    <div class="modal fade" id="mdlHistorialTimesummary" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Historial de tareas</h5>
                </div>
                <div class="modal-body">
                    <table id="tableHistorialTimesummary">
                        <thead>
                            <tr class="text-center">
                                <th class="text-center">Titulo</th>
                                <th class="text-center">Horas</th>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php else: ?>
    <!-- Modal -->
    <div class="modal fade" id="mdlHistorialTimesummary" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Historial de tareas</h5>
                </div>
                <div class="modal-body">
                    <table id="tableHistorialTimesummary">
                        <thead>
                            <tr class="text-center">
                                <th class="text-center">Titulo</th>
                                <th class="text-center">Horas</th>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php endif; ?>