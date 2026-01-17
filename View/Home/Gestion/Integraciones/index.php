<?php
require_once __DIR__ . "/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
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
                        <span class="fs-12 badge bg-primary text-light">Gestion - Integraciones</span>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->
        <div class="col-lg-12">
            <div class="card-body d-flex bg-light">
                <div>
                    <span class="badge bg-warning text-dark">Alta Keys</span>
                    <br>
                    <form style="width: 15rem; margin: 0; padding: 0;" action="" id="form_insert_cliente" method="post">

                        <div class="col-lg-12 mt-1">

                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="inputGroup-sizing-sm">HERRAMIENTA</span>

                                <select id="combo_herramienta" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">

                                </select>
                            </div>

                            <div class="input-group input-group-sm mt-1">
                                <span class="input-group-text" id="inputGroup-sizing-sm">SECTOR </span>

                                <select id="combo_sector" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">

                                </select>
                            </div>
                        </div>

                        <div>
                            <button type="button" id="btnCrearApiKey" name="btnCrearApiKey"
                                class="mt-2 btn btn-warning text-dark waves-effect waves-info btn-sm"
                                style="width: 100%;">Crear</button>
                        </div>

                    </form>
                </div>
                <div class="card card-body" style="margin-left: 5px; margin-top: 1.5rem;">
                    <div class=" col-lg-12">
                        <table id="table_integraciones_api_keys" style="text-align: center; width: 100%;">
                            <thead style="text-align: center;">
                                <tr style="text-align: center;">
                                    <th style="width: 50%;text-align: center;">API KEY</th>
                                    <th style="width: 5%;text-align: center;">HERRAMIENTA</th>
                                    <th style="width: 10%;text-align: center;">SECTOR</th>
                                    <th style="width: 10%;text-align: center;">CREADOR</th>
                                    <th style="width: 10%;text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody style="text-align: center;">
                                <tr style="text-align: center;">
                                    <td style="width: 50%px;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 10%;"></td>
                                    <td style="width: 10%;"></td>
                                </tr>
                            </tbody>
                        </table>
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
    header("Location:" . URL . "/../View/Home/Logout.php");
} ?>