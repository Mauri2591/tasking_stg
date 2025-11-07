<div class="modal fade" id="ModalUpdateCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Editar Cliente - <span id="client_rs_title_update"
                        class="text-primary fs-12"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" id="form_update_cliente" method="post">
                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text" id="inputGroup-sizing-sm">NOMBRE<span
                                class="badge text-danger p-0 m-0 fs-12">*</span></span>
                        <input id="client_rs_update" type="text" class="form-control" aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm">
                    </div>
                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text" id="inputGroup-sizing-sm">C U I T</span>
                        <input id="client_cuit_update" type="text" class="form-control"
                            aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text" id="inputGroup-sizing-sm">CORREO</span>
                        <input id="client_correo_update" type="text" class="form-control"
                            aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>
                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text" id="inputGroup-sizing-sm">TELEFONO</span>
                        <input id="client_tel_update" type="text" class="form-control" aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm">
                    </div>
                    <input type="hidden" hidden id="client_id_update" value="">
                    <div>
                        <button type="button" id="btnUpdateCliente" name="btnIngresarCliente"
                            class="mt-3 btn btn-success waves-effect waves-success btn-sm"
                            style="width: 100%;">Guardar</button>
                    </div>
                </form>
                <div id="modal_cont_mje_campos_obligatorios_vacios_update_cliente">
                </div>
            </div>
        </div>
    </div>
</div>