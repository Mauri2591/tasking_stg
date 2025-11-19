<?php
require_once __DIR__ . "/../../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../../Model/Clases/Headers.php";
    require_once __DIR__ . "/../../../../../Model/Clases/Openssl.php";

    Headers::get_cors();
?>

    <?php
    include_once __DIR__ . "/../../../Public/Template/head.php";
    include_once __DIR__ . "/../../../Public/Template/main_content.php";

    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Proyectos<span class="badge bg-dark text-light"
                                id="client_id_consultar_proyectos"></span>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <!-- container-fluid -->

        <?php
        if ($_SESSION['sector_id'] == "4"):
        ?>
            <!-- container-fluid -->
            <div class="col-lg-12" style="margin-left: 5px;">
                <div class="card-body">
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_borradores" role="tab"
                                aria-selected="true">
                                <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                                <span class="d-none d-sm-block">Borradores</span>
                            </a>
                        </li>
                        <?php if (isset($_SESSION['sector_id']) && $_SESSION['sector_id'] == "4"): ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_recurrencia" role="tab" aria-selected="true">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                                    <span class="d-none d-sm-block">Recurrentes</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_en_proceso" role="tab" aria-selected="false">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                <span class="d-none d-sm-block">En Proceso</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_realizados" role="tab" aria-selected="false">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                <span class="d-none d-sm-block">Realizados por Sector</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_total" role="tab" aria-selected="false">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                <span class="d-none d-sm-block">Total</span>
                            </a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content text-muted">

                        <div class="tab-pane active" id="tab_borradores" role="tabpanel">
                            <div class="card card-body">
                                <table id="table_proyectos_borrador" style="text-align: center; width: 100%;">
                                    <thead style="text-align: center;">
                                        <tr style="text-align: center;">
                                            <th style="width: 100px;text-align: center;">INICIO</th>
                                            <th style="width: 100px;text-align: center;">PRIORIDAD</th>
                                            <th style="width: 400px;text-align: center;">CLIENTE</th>
                                            <th style="width: 20px;text-align: center;">CREADOR</th>
                                            <th style="width: 20px;text-align: center;">REC</th>
                                            <th style="width: 20px;text-align: center;">RECH</th>
                                            <th style="width: 20px;text-align: center;">SECTOR</th>
                                            <th style="width: 20px;text-align: center;">PRODUCTO</th>
                                            <th style="width: 20px;text-align: center;">ASIGNADO</th>
                                            <th style="width: 10px;text-align: center;"></th>
                                            <!-- <th style="width: 30px;text-align: center;"></th> -->
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: center;">
                                        <tr style="text-align: center;">
                                            <td style="width: 100px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <!-- <td style="width: 30px;"></td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['sector_id']) && $_SESSION['sector_id'] == "4"): ?>
                            <div class="tab-pane" id="tab_recurrencia" role="tabpanel">
                                <div class="card card-body">
                                    <table id="table_proyectos_recurrencia" style="text-align: center; width: 100%;">
                                        <thead style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <th style="width: 400px;text-align: center;">CLIENTE</th>
                                                <th style="width: 20px;text-align: center;">PRODUCTO</th>
                                                <th style="width: 20px;text-align: center;">TOTAL</th>
                                                <th style="width: 20px;text-align: center;">UTILIZADAS</th>
                                                <th style="width: 10px;text-align: center;">GESTIONAR</th>
                                                <!-- <th style="width: 30px;text-align: center;"></th> -->
                                            </tr>
                                        </thead>
                                        <tbody style="text-align: center;">
                                            <tr style="text-align: center;">
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <td style="width: 30px;"></td>
                                                <!-- <td style="width: 30px;"></td> -->
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="tab-pane" id="tab_total" role="tabpanel">
                            <div class="card card-body">
                                <table id="table_proyectos_total_calidad" style="text-align: center; width: 100%;">
                                    <div style="display: flex; justify-content: end;">
                                        <span class="mb-4"><span class="badge bg-primary fs-12">Reporte Clientes</span><i onclick="mdlDescargarExcelProyectosTotal()" class="ri-file-excel-2-fill text-success fs-22" type="button" title="Descargar documento"></i></span>
                                    </div>
                                    <thead style="text-align: center;">
                                        <tr style="text-align: center;">
                                            <th style="width: 400px;text-align: center;">CLIENTE</th>
                                            <th style="width: 20px;text-align: center;">PROYECTOS</th>
                                            <th style="width: 20px;text-align: center;"></th>

                                        </tr>
                                    </thead>
                                    <tbody style="text-align: center;">
                                        <tr style="text-align: center;">
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p style="font-style: italic;" class="text-center mt-5 mx-3 text-danger">Los proyectos presentes corresponden a la nueva version de <strong>Tasking</strong></p>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab_en_proceso" role="tabpanel">
                            <div class="card card-body">
                                <table id="table_proyectos_en_proceso" style="text-align: center; width: 100%;">
                                    <thead style="text-align: center;">
                                        <tr style="text-align: center;">
                                            <th style="width: 10px;text-align: center;">INICIO</th>
                                            <th style="width: 10px;text-align: center;">PRIO</th>
                                            <th style="width: 300px;text-align: center;">TITULO</th>
                                            <th style="width: 10px;text-align: center;">FIN</th>
                                            <th style="width: 10px;text-align: center;">CREADOR</th>
                                            <th style="width: 10px;text-align: center;">REC</th>
                                            <th style="width: 10px;text-align: center;">RECH</th>
                                            <th style="width: 10px;text-align: center;">PROD</th>
                                            <th style="width: 10px;text-align: center;">TIPO</th>
                                            <th style="width: 10px;text-align: center;">HS</th>
                                            <th style="width: 10px;text-align: center;">ASIG</th>
                                            <th style="width: 10px; text-align: center;">ESTADO</th>
                                            <th style="width: 10px;text-align: center;">SECTOR</th>
                                            <th style="width: 10px;text-align: center;">HOSTS</th>
                                            <th style="width: 10px;text-align: center;"></th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: center;">
                                        <tr style="text-align: center;">
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 300px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                            <td style="width: 10px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab_realizados" role="tabpanel">
                            <div class="card card-body">
                                <table id="table_proyectos_realizados" style="text-align: center; width: 100%;">
                                    <thead style="text-align: center;">
                                        <tr style="text-align: center;">
                                            <th style="width: 300px;text-align: center;">TITULO</th>
                                            <th style="width: 300px;text-align: center;">INICIO</th>
                                            <th style="width: 30px;text-align: center;">FIN</th>
                                            <th style="width: 30px;text-align: center;">CREADOR</th>
                                            <th style="width: 30px;text-align: center;">PRODUCTO</th>
                                            <th style="width: 30px;text-align: center;">TIPO</th>
                                            <th style="width: 30px;text-align: center;">HS</th>
                                            <th style="width: 30px;text-align: center;">ASIGNADO</th>
                                            <th style="width: 30px;text-align: center;">HOSTS</th>
                                            <th style="width: 30px;text-align: center;">Ver</th>
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
                        include_once __DIR__ . "/Modals/ModalAjustarProject.php";
                        include_once __DIR__ . "/Modals/ModalConsultarActivos.php";
                        include_once __DIR__ . "/Modals/ModalAgregarActivos.php";
                        include_once __DIR__ . "/Modals/ModalVerHosts.php";
                        include_once __DIR__ . "/Modals/ModalGestionRecurrencias.php";
                        include_once __DIR__ . "/Modals/ModalRechequeo.php";
                        include_once __DIR__ . "/Modals/ModalHistorialProyectosCalidad.php";
                        include_once __DIR__ . "/Modals/ModalDescargarReporteXlsx.php";
                        ?>
                        <div class="tab-pane" id="arrow-contact" role="tabpanel">

                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- container-fluid -->
            <!-- container-fluid -->
            <div class="col-lg-12" style="margin-left: 5px;">
                <div class="card-body">
                    <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_total" role="tab" aria-selected="true">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                <span class="d-none d-sm-block">Total</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        <div class="tab-pane fade show active" id="tab_total" role="tabpanel">
                            <div class="card card-body">
                                <table id="table_proyectos_total_calidad" style="text-align: center; width: 100%;">
                                    <div style="display: flex; justify-content: end;">
                                        <span class="mb-4"><span class="badge bg-primary fs-12">Reporte Clientes</span><i onclick="mdlDescargarExcelProyectosTotal()" class="ri-file-excel-2-fill text-success fs-22" type="button" title="Descargar documento"></i></span>
                                    </div>
                                    <thead style="text-align: center;">
                                        <tr>
                                            <th style="width: 400px;text-align: center;">CLIENTE</th>
                                            <th style="width: 20px;text-align: center;">PROYECTOS</th>
                                            <th style="width: 20px;text-align: center;"></th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: center;">
                                        <tr>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                            <td style="width: 30px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p style="font-style: italic;" class="text-center mt-5 mx-3 text-danger">
                                    Los proyectos presentes corresponden a la nueva versión de <strong>Tasking</strong>
                                </p>
                            </div>
                        </div>

                        <?php
                        include_once __DIR__ . "/Modals/ModalAjustarProject.php";
                        include_once __DIR__ . "/Modals/ModalConsultarActivos.php";
                        include_once __DIR__ . "/Modals/ModalAgregarActivos.php";
                        include_once __DIR__ . "/Modals/ModalVerHosts.php";
                        include_once __DIR__ . "/Modals/ModalGestionRecurrencias.php";
                        include_once __DIR__ . "/Modals/ModalRechequeo.php";
                        include_once __DIR__ . "/Modals/ModalHistorialProyectosCalidad.php";
                        include_once __DIR__ . "/Modals/ModalDescargarReporteXlsx.php";
                        ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
    </div>
    <!-- End Page-content -->
    <?php
    include_once __DIR__ . "/../../../Public/Template/footer.php";
    ?>
<?php } else {
    header("Location:" . URL . "/View/Home/Logout.php");
}
?>

<script src="main.js?sheet=<?php echo rand() ?>"></script>