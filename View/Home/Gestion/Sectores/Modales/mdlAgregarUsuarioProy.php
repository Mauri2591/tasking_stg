<div class="modal fade" id="ModalAgregarUsuarioProy" tabindex="-1" aria-hidden="true">
    <input type="hidden" hidden id="id_proyecto_gestionado" value="<?php echo Openssl::get_ssl_decrypt($_GET['p']) ?>">
    <div class="modal-dialog">
        <!-- Modal más ancho -->
        <div class="modal-content">
            <div class="modal-header">
                <!-- <h6>Hosts</h6> -->
                <span class="badge bg-light border border-primary text-primary">Agregar nuevo Usuario</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <select id="combo_usuarios_agregar_proy" class="form-select form-select-sm"
                        aria-label=".form-select-sm example">

                    </select>
                </div>

            </div>
            <button onclick="insert_usuarios_proyecto()" class="btn btn-sm btn-success mb-2 mx-5">Guardar</button>
        </div>
    </div>
</div>