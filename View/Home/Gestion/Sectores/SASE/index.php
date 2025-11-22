<?php
require_once __DIR__ . "/../../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../../Model/Clases/Headers.php";
    Headers::get_cors();
?>
    <?php
    include_once __DIR__ . "/../../../Public/Template/head.php";
    include_once __DIR__ . "/../../../Public/Template/main_content.php";
    ?>
    <div class="page-content">
        <div class="container-fluid">

            <!-- Título -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">
                            <span class="badge bg-danger text-light">SASE</span>
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="col-lg-12">
                <div class="card-body d-flex bg-light p-0">
                    <div class="col-lg-12">
                        <div class="card-body">
                            <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_nuevos" role="tab">
                                        <span class="d-none d-sm-block">Nuevos</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_abiertos" role="tab">
                                        <span class="d-none d-sm-block">Abiertos</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_realizados" role="tab">
                                        <span class="d-none d-sm-block">Realizados</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content text-muted">

                                <!-- NUEVOS -->
                                <div class="tab-pane active" id="tab_nuevos" role="tabpanel">
                                    <div class="card card-body">
                                        <table id="table_proyectos_nuevos_sase" class="table text-center w-100">
                                            <thead>
                                                <tr>
                                                    <th>TITULO</th>
                                                    <th>INICIO</th>
                                                    <th>FIN</th>
                                                    <th>CREADOR</th>
                                                    <th>SERVICIO</th>
                                                    <th>TIPO</th>
                                                    <th>HS</th>
                                                    <th>ASIGNADO</th>
                                                    <th>HOSTS</th>
                                                    <th>ESTADO</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- ABIERTOS -->
                                <div class="tab-pane" id="tab_abiertos" role="tabpanel">
                                    <div class="card card-body">
                                        <table id="table_proyectos_abiertos_sase" class="table text-center w-100">
                                            <thead>
                                                <tr>
                                                    <th>CLIENTE</th>
                                                    <th>INICIO</th>
                                                    <th>FIN</th>
                                                    <th>CREADOR</th>
                                                    <th>SERVICIO</th>
                                                    <th>TIPO</th>
                                                    <th>HS</th>
                                                    <th>ASIGNADO</th>
                                                    <th>HOSTS</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- REALIZADOS -->
                                <div class="tab-pane" id="tab_realizados" role="tabpanel">
                                    <div class="card card-body">
                                        <table id="table_proyectos_realizados_sase" class="table text-center w-100">
                                            <thead>
                                                <tr>
                                                    <th>CLIENTE</th>
                                                    <th>INICIO</th>
                                                    <th>FIN</th>
                                                    <th>CREADOR</th>
                                                    <th>SERVICIO</th>
                                                    <th>TIPO</th>
                                                    <th>HS</th>
                                                    <th>ASIGNADO</th>
                                                    <th>HOSTS</th>
                                                    <th>VER</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Modals -->
                                <?php include_once __DIR__ . "/Modals/ModalVerHosts.php"; ?>
                                <!-- <?php include_once __DIR__ . "/Modals/ModalAgregarActivos.php"; ?> -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include_once __DIR__ . "/../../../Public/Template/footer.php"; ?>
    <script src="main.js?sheet=<?php echo rand(); ?>"></script>
<?php
} else {
    header("Location:" . URL . "/View/Home/Logout.php");
}
?>