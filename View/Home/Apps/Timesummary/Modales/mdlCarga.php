<!-- Modal -->
<div class="modal fade" id="mdlCarcaTimesummary" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <span id="fechaSeleccionada" class="badge fs-11 text-light border bg-primary"></span>
      </div>
      <span class="badge bg-success text-light" style="display: none;width: 7%; margin-left: 1.3rem;" id="validar_si_tiene_id_pm_calidad_es_pm">PM</span>
      <span class="badge bg-danger text-light" style="display: none;width: 25%; margin-left: 1.3rem;" id="validar_si_tiene_id_pm_calidad_proy_asignado">PROYECTO ASIGNADO</span>
      <div class="modal-body">
        <input type="hidden" hidden id="id_pm_calidad">
        <section class="card-body border border-ligth p-2 mt-2">
          <div style="display: flex; justify-content: space-evenly;">
            <div class="mb-2 row">
              <label for="hora_desde" class="col-sm-3 col-form-label"> Desde</label>
              <div class="col-sm-7 ml-2">
                <input type="time" class="form-control form-control-sm" id="hora_desde">
              </div>
            </div>
            <div class="mb-2 row">
              <label for="hora_hasta" class="col-sm-3 col-form-label">Hasta</label>
              <div class="col-sm-7 ml-2">
                <input type="time" class="form-control form-control-sm" id="hora_hasta">
              </div>
            </div>
          </div>
          <div class="mb-2 row">
            <label for="hora_hasta" class="col-sm-2 col-form-label">Proyecto</label>
            <div class="col-sm-10">
              <select class="form-select form-select-sm" id="id_proyecto_gestionado" aria-label=".form-select-sm example">

              </select>
            </div>
          </div>

          <div class="mb-2 row">
            <label for="hora_hasta" class="col-sm-2 col-form-label">Producto</label>
            <div class="col-sm-10">
              <select class="form-select form-select-sm" id="id_producto" aria-label=".form-select-sm example">

              </select>
            </div>
          </div>

          <div class="mb-2 row">
            <label for="hora_hasta" class="col-sm-2 col-form-label">Tarea</label>
            <div class="col-sm-10">
              <select class="form-select form-select-sm" id="id_tarea" aria-label=".form-select-sm example">

              </select>
            </div>
          </div>

          <div class="mb-2">
            <label for="descripcion" class="col-form-label">Descripcion</label>
            <textarea class="form-control" rows="5" id="descripcion">

               </textarea>
          </div>

        </section>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" id="btnGuardarTarea" class="btn btn-sm btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="mdlEditarTimesummary" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <span id="fechaSeleccionadaEdit" class="badge fs-11 text-light border bg-primary"></span>
      </div>
      <div class="modal-body">

        <section class="card-body border border-ligth p-2 mt-2">
          <div style="display: flex; justify-content: space-evenly;">
            <div class="mb-2 row">
              <label for="hora_desde" class="col-sm-3 col-form-label"> Desde</label>
              <div class="col-sm-7 ml-2">
                <input type="time" class="form-control form-control-sm" id="hora_desde_edit">
              </div>
            </div>
            <div class="mb-2 row">
              <label for="hora_hasta" class="col-sm-3 col-form-label">Hasta</label>
              <div class="col-sm-7 ml-2">
                <input type="time" class="form-control form-control-sm" id="hora_hasta_edit">
              </div>
            </div>
          </div>

          <div class="mb-2 row">
            <label for="hora_hasta" class="col-sm-2 col-form-label">Proyecto</label>
            <div class="col-sm-10">
              <input id="id_editar_proyecto_gestionado" readonly class="form-control form-control-sm" type="text">
            </div>
          </div>

          <div class="mb-2 row">
            <label for="hora_hasta" class="col-sm-2 col-form-label">Producto</label>
            <div class="col-sm-10">
              <input id="id_editar_producto" readonly class="form-control form-control-sm" type="text">

            </div>
          </div>

          <div class="mb-2 row">
            <label for="hora_hasta" class="col-sm-2 col-form-label">Tarea</label>
            <div class="col-sm-10">
              <select class="form-control form-control-sm" id="id_editar_tarea">

              </select>

            </div>
          </div>

          <div class="mb-2">
            <label for="editar_descripcion" class="col-form-label">Descripcion</label>
            <textarea class="form-control" rows="5" id="editar_descripcion">

               </textarea>
          </div>

        </section>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" id="btnEditarTarea" class="btn btn-sm btn-success">Guardar</button>
        <button type="button" id="btnEliminarTarea" class="btn btn-sm btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>