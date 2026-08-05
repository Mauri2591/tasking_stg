<?php
require_once __DIR__ . "/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../Model/Clases/Headers.php";

    Headers::get_cors();
?>

    <?php
    include_once __DIR__ . "/../../../../View/Home/Public/Template/head.php";
    include_once __DIR__ . "/../../../../View/Home/Public/Template/main_content.php";
    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light">
                        <span class="fs-12 badge bg-primary text-light">Gestion - Activos</span>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->
        <div class="col-lg-12">
            <!-- <button id="altaActivo" class="btn btn-sm btn-info my-2">Nuevo Activo</button> -->
            <button id="copiarActivos" class="btn btn-sm btn-success my-2" title="Copiar todos los hosts">
                Copiar Hosts <i class="ri-file-copy-fill"></i>
            </button>
            <div class="card p-5">
                <table id="activos_calidad">
                    <thead style="text-align: center;">
                        <tr style="text-align: center;">
                            <th style="width: 300px;text-align: center;">HOST</th>
                            <th style="width: 30px;text-align: center;">AMBIENTE</th>
                            <th style="width: 300px;text-align: center;">SECTOR</th>
                            <th style="width: 30px;text-align: center;">ALTA</th>
                            <th style="width: 30px;text-align: center;">USUARIO</th>
                            <!-- <th style="width: 30px;text-align: center;">ACCION</th> -->
                        </tr>
                    </thead>
                    <tbody style="text-align: center;">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <!-- End Page-content -->
    <?php
    include_once __DIR__ . "/../../../../View/Home/Public/Template/footer.php";
    ?>
    <script src="main.js?sheet=<?php echo rand(); ?>"></script>

<?php } else {
    header("Location:" . URL);
    exit;
} ?>