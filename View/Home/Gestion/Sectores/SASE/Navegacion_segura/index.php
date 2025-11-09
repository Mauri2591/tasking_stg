<?php
require_once __DIR__ . "/../../../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../../../Config/Config.php";
    require_once __DIR__ . "/../../../../../../Model/Clases/Headers.php";

    Headers::get_cors();
?>

    <?php
    include_once __DIR__ . "/../../../../Public/Template/head.php";
    include_once __DIR__ . "/../../../../Public/Template/head.php";
    include_once __DIR__ . "/../../../../Public/Template/main_content.php";
    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0"><span class="badge bg-danger text-light">SASE</span><span
                                class="badge bg-dark text-light border mx-1 border-dark">Navegacion Segura</span>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->
        <div class="col-lg-12">
            <div class="card-body d-flex bg-light p-0">
                <div class="col-lg-12">
                    <div class="card-body">
                        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">

                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab_nuevos" role="tab"
                                    aria-selected="false">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                    <span class="d-none d-sm-block">Nuevos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_abiertos" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-email"></i></span>
                                    <span class="d-none d-sm-block">Abiertos</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_realizados" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-email"></i></span>
                                    <span class="d-none d-sm-block">Realizados</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_cerrados_calidad" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-email"></i></span>
                                    <span class="d-none d-sm-block">Cerrados Calidad</span>
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="tab_nuevos" role="tabpanel">
                                <div class="card card-body">
                                    <table id="table_proyectos_nuevos_sase_navegacion_segura" style="text-align: center; width: 100%;">
                                        <thead style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <th style="width: 300px;text-align: center;">TITULO</th>
                                                <th style="width: 300px;text-align: center;">INICIO</th>
                                                <th style="width: 30px;text-align: center;">FIN</th>
                                                <th style="width: 30px;text-align: center;">REC</th>
                                                <th style="width: 30px;text-align: center;">RECH</th>
                                                <th style="width: 30px;text-align: center;">CREADOR</th>
                                                <th style="width: 30px;text-align: center;">TIPO</th>
                                                <th style="width: 30px;text-align: center;">HS</th>
                                                <th style="width: 30px;text-align: center;">ASIGNADO</th>
                                                <th style="width: 30px;text-align: center;">HOSTS</th>
                                                <th style="width: 30px;text-align: center;"></th>
                                            </tr>
                                        </thead>
                                        <tbody style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <td style="width: 300px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <?php
                            include_once __DIR__ . "/../Modals/ModalVerHosts.php";
                            ?>
                            <div class="tab-pane" id="tab_abiertos" role="tabpanel">
                                <div class="card card-body">
                                    <table id="table_proyectos_abiertos_sase_navegacion_segura" style="text-align: center; width: 100%;">
                                        <thead style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <th style="width: 300px;text-align: center;">TITULO</th>
                                                <th style="width: 300px;text-align: center;">INICIO</th>
                                                <th style="width: 30px;text-align: center;">FIN</th>
                                                <th style="width: 30px;text-align: center;">CREADOR</th>
                                                <th style="width: 30px;text-align: center;">TIPO</th>
                                                <th style="width: 30px;text-align: center;">HS</th>
                                                <th style="width: 30px;text-align: center;">ASIGNADO</th>
                                                <th style="width: 30px;text-align: center;">HOSTS</th>
                                                <th style="width: 30px;text-align: center;"></th>
                                                <th style="width: 30px;text-align: center;"></th>
                                            </tr>
                                        </thead>
                                        <tbody style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <td style="width: 300px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_realizados" role="tabpanel">
                                <div class="card card-body">
                                    <table id="table_proyectos_realizados_sase_navegacion_segura" style="text-align: center; width: 100%;">
                                        <thead style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <th style="width: 300px;text-align: center;">TITULO</th>
                                                <th style="width: 300px;text-align: center;">INICIO</th>
                                                <th style="width: 30px;text-align: center;">FIN</th>
                                                <th style="width: 30px;text-align: center;">CREADOR</th>
                                                <th style="width: 30px;text-align: center;">TIPO</th>
                                                <th style="width: 30px;text-align: center;">HS</th>
                                                <th style="width: 30px;text-align: center;">ASIGNADO</th>
                                                <th style="width: 30px;text-align: center;">HOSTS</th>
                                                <th style="width: 30px;text-align: center;"></th>
                                            </tr>
                                        </thead>
                                        <tbody style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <td style="width: 300px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            <div class="tab-pane" id="tab_cerrados_calidad" role="tabpanel">
                                <div class="card card-body">
                                    <table id="table_proyectos_cerrados_calidad_sase_navegacion_segura"
                                        style="text-align: center; width: 100%;">
                                        <thead style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <th style="width: 300px;text-align: center;">TITULO</th>
                                                <th style="width: 300px;text-align: center;">INICIO</th>
                                                <th style="width: 30px;text-align: center;">FIN</th>
                                                <th style="width: 30px;text-align: center;">CREADOR</th>
                                                <th style="width: 30px;text-align: center;">TIPO</th>
                                                <th style="width: 30px;text-align: center;">HS</th>
                                                <th style="width: 30px;text-align: center;">ASIGNADO</th>
                                                <th style="width: 30px;text-align: center;">HOSTS</th>
                                                <th style="width: 30px;text-align: center;"></th>
                                            </tr>
                                        </thead>
                                        <tbody style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <td style="width: 300px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page-content -->
    <?php
    include_once __DIR__ . "/../../../../Public/Template/footer.php";
    ?>
    <script src="main.js?sheet=<?php echo rand(); ?>"></script>

<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
} ?>