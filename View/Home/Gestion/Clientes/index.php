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
                        <h4 class="mb-sm-0">Gestion - Clientes</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->
        <div class="col-lg-12">
            <div class="card-body d-flex bg-light">
                <div class="border-light">
                    <span class="badge bg-info">Alta cliente</span>
                    <br>
                    <form style="width: 90%; margin: 0; padding: 0;" action="" id="form_insert_cliente" method="post">
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">NOMBRE<span
                                    class="badge text-danger p-0 m-0 fs-12">*</span></span>
                            <input id="client_rs" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm">
                        </div>
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">C U I T</span>
                            <input id="client_cuit" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm">
                        </div>
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">CORREO</span>
                            <input id="client_correo" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm">
                        </div>
                        <div class="input-group input-group-sm mt-1">
                            <span class="input-group-text" id="inputGroup-sizing-sm">TELEFONO</span>
                            <input id="client_tel" type="text" class="form-control" aria-label="Sizing example input"
                                aria-describedby="inputGroup-sizing-sm">
                        </div>

                        <div class="col-lg-12 mt-1">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="inputGroup-sizing-sm">PAIS <span
                                        class="text-danger fs-14"> *</span></span>
                                <select id="combo_paises" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">

                                </select>
                            </div>
                        </div>

                        <div>
                            <button type="button" id="btnIngresarCliente" name="btnIngresarCliente"
                                class="mt-2 btn btn-info waves-effect waves-info btn-sm"
                                style="width: 100%;">Agregar</button>
                        </div>
                        <div class="text-center"> <code>Los campos con (*) son obligatorios</code>
                        </div>
                        <div id="cont_mje_campos_obligatorios_vacios_insert_cliente">
                        </div>
                    </form>
                </div>
                <div class="card card-body" style="margin-left: 5px;">
                    <div class=" col-lg-12">
                        <table id="table_clientes" style="text-align: center; width: 100%;">
                            <thead style="text-align: center;">
                                <tr style="text-align: center;">
                                    <th style="width: 30%;text-align: center;">CLIENTE</th>
                                    <th style="width: 10%;text-align: center;">CUIT</th>
                                    <th style="width: 10%;text-align: center;">CORREO</th>
                                    <th style="width: 10%;text-align: center;">TEL</th>
                                    <th style="width: 10%;text-align: center;">CREAR</th>
                                    <th style="width: 10%;text-align: center;">PROY's</th>
                                    <th style="width: 10%;text-align: center;">EDIT</th>

                                </tr>
                            </thead>
                            <tbody style="text-align: center;">
                                <tr style="text-align: center;">
                                    <td style="width: 30%px;"></td>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 10%;"></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php
                        include_once __DIR__ . "/Modals/ModalUpdateCliente.php";
                        include_once __DIR__ . "/Proyectos/Modals/ModalAltaProject.php";
                        ?>
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