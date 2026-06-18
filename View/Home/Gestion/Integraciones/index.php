<?php
require_once __DIR__ . "/../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../Config/Config.php";
if (isset($_SESSION['usu_id'])) {
    require_once __DIR__ . "/../../../../Model/Clases/Headers.php";
    Headers::get_cors();
?>

    <?php
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
            <!-- Nav tabs -->
            <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#powerbi" role="tab">
                        <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                        <span class="d-none d-sm-block">Power Bi</span>
                    </a>
                </li>
                <?php if($_SESSION['usu_id'] == '104'): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#genAi" role="tab">
                        <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                        <span class="d-none d-sm-block">GenAi</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active show mt-4" id="powerbi" role="tabpanel">
                    <div class="d-flex">
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
                                            <th style="width: 10%;text-align: center;">PM</th>
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

                <?php if($_SESSION['usu_id'] == '104'): ?>

                <div class="tab-pane" id="genAi" role="tabpanel">
                    <div class="d-flex">
                        <div class="card col-lg-12">
                            <div class="card-body">
                                <p class="text-muted">Vista para realizar pruebas. <code>Mauricio Raul Gonzalez - <strong> GenAi</strong></code> </p>
                                <!-- Nav tabs -->
                                <ul class="nav nav-pills nav-custom-outline nav-primary mb-3" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#consultarGenAiModelos" role="tab" aria-selected="true">Consultar Modelos</a>
                                    </li>

                                      <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link" data-bs-toggle="tab" href="#testGenAi" role="tab" aria-selected="false">Chat</a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content text-muted col-lg-12">

                                    <div class="tab-pane" id="testGenAi" role="tabpanel">
                                        <div class="mb-2">
                                            <label class="form-label fw-bold">API Key (JWT)</label>
                                            <input type="text" id="genai_api_key" class="form-control form-control-sm" placeholder="eyJhbGci...">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-bold">Agente</label>
                                            <input id="genai_agente" class="form-control form-control-sm">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-bold">Prompt</label>
                                            <textarea id="genai_prompt" class="form-control form-control-sm" rows="3"></textarea>
                                        </div>

                                        <button id="btn_genai_enviar" class="btn btn-secondary btn-sm">Chat</button>
                                        <button id="btn_genai_enviar" class="btn btn-primary btn-sm" style="display:none;">Enviar</button>

                                        <div id="genai_spinner" class="mt-2" style="display:none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                            <span class="ms-1">Chateando con modelo...</span>
                                        </div>

                                        <div id="genai_resultado" class="mt-3" style="display:none;">
                                            <i class="ri-bilibili-line fs-4 text-primary me-1"></i>
                                            <span class="badge bg-success">HTTP 200</span>
                                            <small id="genai_chat_id" class="text-muted ms-2"></small>
                                            <div class="alert alert-success mt-2" style="white-space: pre-wrap;" id="genai_respuesta_texto"></div>
                                        </div>
                                    </div>

                                    <div class="tab-pane active" id="consultarGenAiModelos" role="tabpanel">
                                        <div class="mb-2" style="max-width: 700px;">
                                            <label class="form-label fw-bold">API Key (JWT)</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="cm_api_key" class="form-control form-control-sm" placeholder="eyJhbGci...">
                                                <button id="btn_cm_consultar" class="btn btn-primary btn-sm">Consultar modelos</button>
                                            </div>
                                        </div>

                                        <div id="cm_spinner" class="mt-2" style="display:none;">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                            <span class="ms-1">Cargando modelos...</span>
                                        </div>

                                        <div id="cm_resultado" class="mt-3" style="display:none;">
                                            <span class="badge bg-success mb-2">HTTP 200</span>
                                            <table class="table table-sm table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Modelo</th>
                                                        <th>Modo</th>
                                                        <th>Descripción</th>
                                                        <th>Costo input/token</th>
                                                        <th>Costo output/token</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cm_tabla_body"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- End Page-content -->
    <?php
    include_once __DIR__ . "/../../Public/Template/footer.php";
    ?>
    <script src="main.js?sheet=<?php echo rand() ?>"></script>

<?php } else {
    header("Location:" . URL);
    exit;
} ?>