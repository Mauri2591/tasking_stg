<?php
require_once __DIR__ . "/../../../../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../../../../Model/Clases/Headers.php";

    Headers::get_cors();
?>

    <?php
    include_once __DIR__ . "/../../../../../Public/Template/head.php";
    include_once __DIR__ . "/../../../../../Public/Template/main_content.php";
    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-light">
                        <h4 class="mb-sm-0"><span class="badge bg-warning text-dark border border-dark">ETHICAL
                                HACKING</span><span class="badge bg-dark text-light border mx-1 border-dark">ACTIVOS</span>
                        </h4>
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
                <table id="activos_eh">
                    <thead style="text-align: center;">
                        <tr style="text-align: center;">
                            <th style="width: 300px;text-align: center;">HOST</th>
                            <th style="width: 30px;text-align: center;">AMBIENTE</th>
                            <th style="width: 300px;text-align: center;">CALIDAD</th>
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
    include_once __DIR__ . "/../../../../../Public/Template/footer.php";
    ?>
    <script src="main.js?sheet=<?php echo rand(); ?>"></script>

<?php } else {
    header("Location:" . URL);
    exit;
} ?>