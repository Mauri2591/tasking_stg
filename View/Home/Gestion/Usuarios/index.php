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
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Gestion - Usuarios</h4>
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
                    <form action="" id="form_insert_usuario" method="post">
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">NOMBRE<span
                                    class="badge text-danger p-0 m-0 fs-12">*</span></span>
                            <input id="usu_nom" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm">
                        </div>
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">CORREO</span>
                            <input id="usu_correo" type="email" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm">
                        </div>
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">TELEFONO</span>
                            <input id="usu_tel" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm">
                        </div>

                        <div class="mt-1 mb-1">
                            <select id="combo_usuarios" class="form-select form-select-sm"
                                aria-label=".form-select-sm example">

                            </select>
                        </div>

                        <div>
                            <button type="button" id="btnIngresarUsuario" name="btnIngresarUsuario"
                                class="mt-2 btn btn-success waves-effect waves-success btn-sm"
                                style="width: 100%;">Agregar</button>
                        </div>
                        <div class="text-center"> <code>Los campos con (*) son obligatorios</code>
                        </div>
                        <div id="cont_mje_campos_obligatorios_vacios_insert_usuario">
                        </div>
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