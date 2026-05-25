<div class="modal fade" id="ModalEnviarCorreoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 95vw; margin-top: .5rem;">
        <div class="modal-content">

            <div class="card ribbon-box shadow-none mb-lg-0">
                <div class="card-body" style="position: relative;">

                    <div class="ribbon ribbon-secondary py-0 ribbon-shape d-flex align-items-center">
                        Envio de Correos <i class="ri-mail-send-line fs-18 mx-1"></i>
                    </div>

                    <?php if ($estado_actual): ?>
                        <span class="fs-14 mt-2 mx-2" style="position: absolute; top: 0; right: 0;">
                            <span class="badge" style="background-color: <?= $estado_actual['CatColor'] ?>;">
                                <i class="<?= $estado_actual['icono'] ?>"></i>
                                ESTADO <?= htmlspecialchars($estado_actual['estados_nombre']) ?>
                            </span>
                        </span>
                    <?php endif; ?>
                    <br>
                    <div class="bg-light rounded p-2 border mt-2 text-center">
                        <span class="text-muted fs-16">
                            Los siguientes documentos serán enviados al cliente
                        </span>
                        <strong class="mb-0 fs-16 fw-bold text-dark">
                            <?= htmlspecialchars(strtoupper($client_rs ?? '')) ?>
                        </strong>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3" style="height: 500px;">

                    <!-- Lista de documentos -->
                    <div style="width: 25rem; border-right: 1px solid #ddd; overflow-y: auto; border:.1rem solid #ddd; border-radius: .7rem;">

                        <p class="fw-bold fs-13 px-2 pt-2">Informacion asociada al cliente:</p>

                        <?php if ($_SESSION['sector_id'] == '4'): ?>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark fs-11 border-dark mx-2"><span class="text-danger">*</span> Correo:</span>
                                <input type="text" id="correo_envio_email" name="correo_envio_email" class="form-control form-control-sm" style="width: 15rem;" value="<?= htmlspecialchars($correo_envio_cliente ?? '') ?>">
                            </div>
                        <?php endif; ?>

                        <?php
                        $archivos = [];
                        $carpeta  = '';
                        $doc      = null;
                        if (!empty($documentos_envio_cliente)) {
                            $doc      = $documentos_envio_cliente[0];
                            $carpeta  = $doc['carpeta_documentos_proy'];
                            $archivos = array_filter(explode(',', $doc['documento']));
                        }
                        ?>

                        <?php if (!empty($archivos)): ?>
                            <span class="badge bg-light text-dark fs-11 px-2 mb-1 mt-3 mx-2">Fecha de carga de los Docs: <span class="fs-12 border"><?= htmlspecialchars($doc['fech_crea']) ?></span></span>
                            <?php foreach ($archivos as $archivo): ?>
                                <?php
                                $ext   = strtolower(pathinfo(trim($archivo), PATHINFO_EXTENSION));
                                $icono = match ($ext) {
                                    'pdf'                => 'ri-file-pdf-line text-danger',
                                    'doc', 'docx'        => 'ri-file-word-line text-primary',
                                    'xls', 'xlsx'        => 'ri-file-excel-line text-success',
                                    'jpg', 'jpeg', 'png' => 'ri-image-line text-warning',
                                    'zip'                => 'ri-file-zip-line text-secondary',
                                    default              => 'ri-file-line text-muted'
                                };
                                ?>
                                <div class="d-flex align-items-center gap-2 px-2 py-1">
                                    <i class="<?= $icono ?> fs-16"></i>
                                    <span class="fs-12" style="word-break: break-all;"><?= htmlspecialchars(trim($archivo)) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted fs-13 px-2">Sin documentos adjuntos</p>
                        <?php endif; ?>

                        <hr>
                        <span class="badge bg-light text-dark fs-11 border-dark mx-2">Historial de envíos:</span>
                        <div class="card-body" style="width: 100%; overflow-y: auto; padding: 1rem;">

                            <?php if (!empty($datos_envios_agrupados)): ?>
                                <?php foreach ($datos_envios_agrupados as $idx => $val): ?>

                                    <div class="border rounded p-2 mb-2" style="background:#fafafa;">
                                        <span class="badge bg-light text-dark fs-10 mb-1">Envío #<?= $idx + 1 ?></span>

                                        <!-- CORREO AL CLIENTE -->
                                        <div class="fs-13" id="correo_item_<?= intval($val['id']) ?>">
                                            <strong>Cliente:</strong>
                                            <span class="badge_status_<?= intval($val['id']) ?> <?= $val['status_envio'] == 'OK' ? 'badge bg-success text-light' : 'badge bg-danger text-light' ?>">
                                                <?= htmlspecialchars($val['status_envio']) ?>
                                            </span><br>
                                            <span class="text-muted fs-11">Por: <?= htmlspecialchars($val['usu_correo']) ?> - <?= htmlspecialchars($val['sector_nombre']) ?></span><br>
                                            <span class="text-muted fs-11">Fecha: <?= htmlspecialchars($val['fech_crea']) ?></span><br>

                                            <?php if ($_SESSION['sector_id'] == '4'): ?>
                                                <?php if (!empty($val['ruta_comprimido'])): ?>
                                                    <a href="<?= htmlspecialchars(ZIP_URL . basename($val['ruta_comprimido'])) ?>" download class="text-decoration-none fs-11">
                                                        <i class="fa-solid fa-file-zipper"></i> Descargar ZIP
                                                    </a><br>
                                                <?php endif; ?>
                                                <span class="fs-11"><strong>Clave: </strong><?= htmlspecialchars($val['clave_comprimido']) ?></span><br>
                                            <?php endif; ?>

                                            <?php if (!empty($val['fech_actualizacion'])): ?>
                                                <span class="badge bg-success text-light fs-10 mt-1 d-inline-block">
                                                    <i class="ri-mail-check-line"></i> Enviado por otro medio
                                                </span>
                                                <span class="text-muted fs-10">el: <?= htmlspecialchars($val['fech_actualizacion']) ?></span>
                                            <?php elseif ($_SESSION['sector_id'] == '4' && $val['status_envio'] == 'ERROR'): ?>
                                                <span><strong class="fs-11">Reenviar por otro medio: </strong>
                                                    <i class="ri-mail-send-line"
                                                        type="button"
                                                        onclick='reenviar_correo(<?= intval($val["id"]) ?>)'
                                                        style="font-size:1.1rem; color:#0d6efd; cursor:pointer;"
                                                        onmouseover="this.style.filter='brightness(1.2)';"
                                                        onmouseout="this.style.filter='brightness(1)';"></i>
                                                </span>
                                            <?php endif; ?>
                                            <br> <br>
                                        </div>

                                        <!-- COPIAS del mismo envío -->
                                        <?php $copias = $internos_por_desc[$val['id']] ?? []; ?>
                                        <?php if (!empty($copias)): ?>
                                            <div class="mt-1 ps-2 border-start border-secondary">
                                                <?php foreach ($copias as $copia): ?>
                                                    <div class="fs-12 my-1" id="correo_interno_item_<?= intval($copia['id']) ?>">
                                                        <strong>Copia:</strong> <?= htmlspecialchars($copia['correo']) ?>
                                                        <span class="badge_status_interno_<?= intval($copia['id']) ?> <?= $copia['status_envio'] == 'OK' ? 'badge bg-success text-light' : ($copia['status_envio'] == 'PENDIENTE' ? 'badge bg-warning text-dark' : 'badge bg-danger text-light') ?> fs-10">
                                                            <?= htmlspecialchars($copia['status_envio']) ?>
                                                        </span><br>
                                                        <span class="text-muted fs-10">Fecha: <?= htmlspecialchars($copia['fech_crea']) ?></span>

                                                        <?php if (!empty($copia['fech_actualizacion'])): ?>
                                                            <br><span class="badge bg-success text-light fs-10">
                                                                <i class="ri-mail-check-line"></i> Enviado por otro medio
                                                            </span>
                                                            <span class="text-muted fs-10">el: <?= htmlspecialchars($copia['fech_actualizacion']) ?></span>
                                                        <?php elseif ($_SESSION['sector_id'] == '4' && in_array($copia['status_envio'], ['ERROR', 'PENDIENTE'])): ?>
                                                            <i class="ri-mail-send-line ms-1"
                                                                type="button"
                                                                onclick='reenviar_correo_interno(<?= intval($copia["id"]) ?>)'
                                                                style="font-size:1rem; color:#0d6efd; cursor:pointer;"
                                                                onmouseover="this.style.filter='brightness(1.2)';"
                                                                onmouseout="this.style.filter='brightness(1)';"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="fs-13 text-muted">Sin envíos registrados</p>
                            <?php endif; ?>

                        </div>
                    </div>

                    <!-- Previsualizador -->
                    <div class="flex-grow-1" style="overflow-y: auto;">
                        <?php if (!empty($archivos)): ?>
                            <?php foreach ($archivos as $archivo): ?>
                                <?php
                                $ext      = strtolower(pathinfo(trim($archivo), PATHINFO_EXTENSION));
                                $url_arch = URL . "View/Home/Public/Uploads/Proyectos/" . $carpeta . "/" . trim($archivo);
                                ?>
                                <div class="mb-3">
                                    <p class="fs-12 text-muted mb-1"><?= htmlspecialchars(trim($archivo)) ?></p>
                                    <?php if ($ext === 'pdf'): ?>
                                        <iframe src="<?= $url_arch ?>" style="width:100%; height:400px; border:1px solid #ddd; border-radius:5px;"></iframe>
                                    <?php elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                        <img src="<?= $url_arch ?>" style="max-width:100%; border-radius:5px;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center gap-2 p-3 border rounded">
                                            <i class="ri-file-line fs-24 text-muted"></i>
                                            <span class="fs-13"><?= htmlspecialchars(trim($archivo)) ?></span>
                                            <a href="<?= $url_arch ?>" download class="btn btn-sm btn-outline-primary ms-auto">
                                                <i class="ri-download-line"></i> Descargar
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if ($_SESSION['sector_id'] == '4'  && !empty($archivos)): ?>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" id="btn_enviar_correo_cliente">Enviar Correo</button>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>