<?php
require_once __DIR__ . "/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../Config/Config.php";
    require_once __DIR__ . "/../../../../Model/Clases/Headers.php";

    Headers::get_cors();
?>

    <?php
    include_once __DIR__ . "/../../Public/Template/head.php";
    include_once __DIR__ . "/../../Public/Template/head.php";
    include_once __DIR__ . "/../../Public/Template/main_content.php";
    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light">
                        <span class="fs-12 badge bg-primary text-light">Gestion - Usuarios</span>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->
        <div class="col-lg-12">
            <div class="card-body d-flex bg-light">
                <div class="col-mx-3 border border-light">
                    <span class="badge bg-success">Alta Usuario</span>

                    <br>
                    <form id="form_insert_usuario" method="post">

                        <!-- NOMBRE -->
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text">
                                NOMBRE <span class="text-danger">*</span>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                required>
                        </div>

                        <!-- Apellido -->
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text">
                                APELLIDO
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="apellido"
                                name="apellido"
                                required>
                        </div>

                        <!-- CORREO -->
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text">
                                CORREO <span class="text-danger">*</span>
                            </span>
                            <input
                                type="email"
                                class="form-control"
                                id="correo"
                                name="correo"
                                required>
                        </div>

                        <!-- TELÉFONO -->
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text">
                                TELÉFONO <span class="text-danger">*</span>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="usu_tel"
                                name="usu_tel"
                                required>
                        </div>

                        <!-- SECTOR -->
                        <div class="mt-1 mb-1">
                            <select
                                id="combo_usuarios"
                                name="sector_id"
                                class="form-select form-select-sm"
                                required>
                                <option value="">Seleccione sector</option>
                                <!-- opciones cargadas por AJAX -->
                            </select>
                        </div>

                        <!-- BOTÓN -->
                        <div>
                            <button
                                type="button"
                                id="btnIngresarUsuario"
                                class="mt-2 btn btn-success btn-sm w-100">
                                Agregar
                            </button>
                        </div>

                        <div class="text-center mt-1">
                            <code>Los campos con (*) son obligatorios</code>
                        </div>

                        <div id="cont_mje_campos_obligatorios_vacios_insert_usuario"></div>

                    </form>


                </div>
                <div class="col-lg-10" style="margin-left: 5px;">
                    <div class="card card-body">
                        <table id="table_usuarios" style="text-align: center; width: 100%;">
                            <thead style="text-align: center;">
                                <tr style="text-align: center;">
                                    <th style="width: 300px;text-align: center;">Nombre</th>
                                    <th style="width: 30px;text-align: center;">CORREO</th>
                                    <th style="width: 30px;text-align: center;">TEL</th>
                                    <th style="width: 30px;text-align: center;">SECTOR</th>
                                    <!-- <th style="width: 30px;text-align: center;">ESTADO</th> -->

                                </tr>
                            </thead>
                            <tbody style="text-align: center;">
                                <tr style="text-align: center;">
                                    <td style="width: 30px;text-align: center;"></td>
                                    <td style="width: 30px;text-align: center;"></td>
                                    <td style="width: 30px;text-align: center;"></td>
                                    <td style="width: 30px;text-align: center;"></td>
                                    <!-- <td style="width: 30px;"></td> -->
                                </tr>
                            </tbody>
                        </table>
                        <!-- <?php
                                // include_once __DIR__ . "/Modals/ModalUpdateCliente.php";
                                ?> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page-content -->
    <?php
    include_once __DIR__ . "/../../Public/Template/footer.php";
    ?>
    <script src="main.js?sheet=<?php echo rand() ?>"></script>

<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
} ?>