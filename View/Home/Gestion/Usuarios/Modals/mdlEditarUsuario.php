<div class="modal fade" id="modal_editar_usuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Editar Usuario</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="post">
                    <input type="hidden" hidden id="usu_id_editar">
                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text" id="inputGroup-sizing-sm">NOMBRE<span
                                class="badge text-danger p-0 m-0 fs-12">*</span></span>
                        <input id="usu_nom_editar" type="text" class="form-control" aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm">
                    </div>

                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text" id="inputGroup-sizing-sm">APELLIDO<span
                                class="badge text-danger p-0 m-0 fs-12">*</span></span>
                        <input id="usu_ape_editar" type="text" class="form-control" aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm">
                    </div>

                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text" id="inputGroup-sizing-sm">EMAIL<span
                                class="badge text-danger p-0 m-0 fs-12">*</span></span>
                        <input id="usu_correo_editar" type="text" class="form-control" aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm">
                    </div>

                    <div class="input-group input-group-sm mt-2 align-items-center">
                        <div class="form-check form-switch me-1">
                            <input class="form-check-input" type="checkbox" id="checkResetClave">
                        </div>
                        <span class="input-group-text flex-shrink-0" id="inputGroup-sizing-sm">
                            RESETEAR PASSWORD<span class="badge text-danger p-0 m-0 fs-12"></span>
                        </span>

                        <input id="usu_pass_editar" disabled name="usu_pass_editar" type="password" class="form-control" aria-describedby="inputGroup-sizing-sm">
                    </div>

                    <div class="input-group input-group-sm mt-1 align-items-center">
                        <span class="input-group-text flex-shrink-0" id="basic-addon1">Estado</span>
                        <select id="editar_estado_usuario" class="form-select form-select-sm mt-2 mb-2" aria-label="Default select example">

                        </select>
                    </div>

                    <div>
                        <button type="button" onclick="boton_editar_usuario()"
                            class="mt-3 btn btn-primary waves-effect waves-success btn-sm"
                            style="width: 100%;">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>