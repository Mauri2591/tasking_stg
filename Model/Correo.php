<?php
require_once __DIR__ . '/../Config/Conexion.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Correo extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDatosParaCorreo($id)
    {
        $conn = $this->get_conexion();
        $sql = "SELECT
        pg.id,
        pg.titulo,
        pg.refProy,
        tm_categoria.cat_nom AS producto,
        tm_subcategoria.cats_nom AS tipo,
        cli.client_rs AS cliente,
        cli.pais_id,
        tm_pais.pais_nombre AS pais_nombre,
        s.sector_nombre AS sector,
        COALESCE(
        GROUP_CONCAT(
            DISTINCT LOWER(tu.usu_correo)
            ORDER BY LOWER(tu.usu_correo)
            SEPARATOR ', '
        ),
        'Sin usuarios asignados'
        ) AS usuarios
        FROM proyecto_gestionado pg
        LEFT JOIN proyecto_cantidad_servicios pcs
            ON pcs.id = pg.id_proyecto_cantidad_servicios
        LEFT JOIN proyectos pr
            ON pr.proy_id = pcs.proy_id
        LEFT JOIN clientes cli
            ON cli.client_id = pr.client_id
        LEFT JOIN sectores s
            ON s.sector_id = pg.sector_id
        LEFT JOIN usuario_proyecto up
            ON up.id_proyecto_gestionado = pg.id
        LEFT JOIN tm_usuario tu
            ON tu.usu_id = up.usu_asignado
        INNER JOIN tm_categoria ON pg.cat_id=tm_categoria.cat_id
        INNER JOIN tm_subcategoria ON pg.cats_id=tm_subcategoria.cats_id
        INNER JOIN tm_pais ON cli.pais_id=tm_pais.pais_id
        WHERE pg.id = :id
        GROUP BY
            pg.id,
            cli.client_rs,
            s.sector_nombre";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function enviarCorreoProyectoFinalizado($id)
    {
        $datos = $this->getDatosParaCorreo($id);

        if (!$datos) {
            return 'No se encontraron datos del proyecto';
        }
        $producto = $datos->producto ?: 'N/A';
        $cliente  = $datos->cliente  ?: 'N/A';
        $refProy  = $datos->refProy  ?: 'N/A';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = false;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)SMTP_PORT;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(SMTP_FROM_ARG, SMTP_FROM_NAME);
            $mail->addAddress('mssp-calidad@personal.com.ar');
            // Agregar usuarios en copia
            if (!empty($datos->usuarios) && $datos->usuarios !== 'Sin usuarios asignados') {
                $listaUsuarios = array_map('trim', explode(',', $datos->usuarios));
                foreach ($listaUsuarios as $correo) {
                    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                        $mail->addCC($correo);
                    }
                }
            }
            $mail->isHTML(true);
            $mail->Subject = 'Proyecto finalizado - [CLIENTE] ' . $cliente;
            $mail->Body = "<p>Estimados,<br><br>
                El presente proyecto se encuentra finalizado correctamente.</p>
                <p><b>Título:</b> {$datos->titulo}</p>
                <p><b>Ref:</b> {$refProy}</p>
                <p><b>Sector:</b> {$datos->sector}</p>
                <p><b>Producto:</b> {$producto}</p>
                <p><b>Tipo:</b> {$datos->tipo}</p>
                <p><b>Usuarios asignados al proyecto:</b><br>{$datos->usuarios}</p>
                <p>
                <b>Envío al cliente:</b><br>
                Los informes fueron cargados en <strong>Tasking</strong>.<br>
                Recuerde enviar el correo al cliente mediante la propia herramienta.
                En caso de falla, podrá realizar el envío por otro medio (por ejemplo, Outlook) y deberá registrarlo en Tasking utilizando el botón <strong>Enviar por otro medio</strong>.
                </p>
                <br>
                <p>Saludos.</p>";
            $mail->send();
            return true;
        } catch (Exception $e) {
            return 'ERROR SMTP: ' . $mail->ErrorInfo;
        }
    }

    private function registrarEnvioInterno(
        int $id_proyecto_gestionado,
        ?int $id_descripciones_proyecto,
        string $correo,
        string $status,
        string $detalle_error = '',
        ?int $id_envio_correo_cliente = null  // ← nuevo parámetro
    ): void {
        $conn = $this->get_conexion();
        $sql  = "INSERT INTO envio_correo_interno 
            (id_descripciones_proyecto, id_proyecto_gestionado, correo, usu_crea, sector_id, status_envio, detalle_error, id_envio_correo_cliente, fech_crea) 
         VALUES 
            (:id_desc, :id, :correo, :usu, :sector, :status, :detalle, :id_ecc, now())";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_desc', $id_descripciones_proyecto, PDO::PARAM_INT);
        $stmt->bindValue(':id',      $id_proyecto_gestionado,    PDO::PARAM_INT);
        $stmt->bindValue(':correo',  $correo,                    PDO::PARAM_STR);
        $stmt->bindValue(':usu',     (int)$_SESSION['usu_id'],   PDO::PARAM_INT);
        $stmt->bindValue(':sector',  (int)$_SESSION['sector_id'], PDO::PARAM_INT);
        $stmt->bindValue(':status',  $status,                    PDO::PARAM_STR);
        $stmt->bindValue(':detalle', $detalle_error,             PDO::PARAM_STR);
        $stmt->bindValue(':id_ecc',  $id_envio_correo_cliente,   PDO::PARAM_INT);
        $stmt->execute();
    }

    private function getCorreosClienteCopia(int $id_proyecto_gestionado, string $correos_override = ''): array
    {
        $conn = $this->get_conexion();
        $sql = "SELECT correo_envio_cliente_copias FROM proyecto_gestionado WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        $proy = $stmt->fetch(PDO::FETCH_ASSOC);

        // Usa el override si viene, sino las de la DB
        $copias_str = !empty($correos_override) ? $correos_override : ($proy['correo_envio_cliente_copias'] ?? '');

        if (!empty($copias_str)) {
            $copias = array_filter(array_map('trim', explode(',', $copias_str)));
            $copias = array_filter($copias, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            return $copias;
        }

        return [];
    }


    private function getCorreosCopia(int $id_proyecto_gestionado, string $correos_override = ''): array
    {
        $conn = $this->get_conexion();
        $sql = "SELECT cat_id, sector_id, correo_envio_cliente_copias FROM proyecto_gestionado WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        $proy = $stmt->fetch(PDO::FETCH_ASSOC);
        $cat_id    = (int)$proy['cat_id'];
        $sector_id = (int)$proy['sector_id'];

        // Siempre mssp-calidad
        $correos = array_filter(array_map('trim', explode(',', MAIL_COPIA_SECTORES)));

        // Copias: usa el override del input si viene, sino las de la DB
        $copias_str = !empty($correos_override) ? $correos_override : ($proy['correo_envio_cliente_copias'] ?? '');
        if (!empty($copias_str)) {
            $copias = array_filter(array_map('trim', explode(',', $copias_str)));
            // ← AGREGAR VALIDACIÓN
            $copias = array_filter($copias, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            $correos = array_merge($correos, $copias);
        }

        // Líderes del sector solo si NO es INCIDENT RESPONSE (cat_id = 26)
        if ($cat_id !== 26) {
            $sql2 = "SELECT usu_correo FROM tm_usuario 
                 WHERE sector_id = :sector_id 
                 AND lider = 'SI' 
                 AND est = 1";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
            $stmt2->execute();
            $lideres = array_column($stmt2->fetchAll(PDO::FETCH_ASSOC), 'usu_correo');
            $correos = array_merge($correos, $lideres);
        }

        return array_unique($correos);
    }

    public function enviarCorreoCliente(int $id_proyecto_gestionado, string $correo_destino, int $pais_id, string $correos_copia_input = '')
    {
        $correos_copia = $this->getCorreosCopia($id_proyecto_gestionado, $correos_copia_input);
        $correos_cliente_copia = $this->getCorreosClienteCopia($id_proyecto_gestionado, $correos_copia_input);
        $remitente = $pais_id == 1 ? SMTP_FROM_ARG : SMTP_FROM_INT;
        $nombre_remitente = $pais_id == 1 ? SMTP_FROM_NAME_ARG : SMTP_FROM_NAME_INT;
        // SMTP base
        $smtpConfig = function (PHPMailer $mail) use ($remitente, $nombre_remitente) {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = false;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)SMTP_PORT;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($remitente, $nombre_remitente);
            $mail->isHTML(true);
        };

        $datos    = $this->getDatosParaCorreo($id_proyecto_gestionado);
        $refProy  = $datos->refProy  ?: 'N/A';
        $producto = $datos->producto ?: 'N/A';
        $tipo = $datos->tipo ?: 'N/A';
        $cliente  = $datos->cliente  ?: 'N/A';

        $conn = $this->get_conexion();
        $sql  = "SELECT descripciones_proyecto.id, 
            descripciones_proyecto.carpeta_documentos_proy, 
            descripciones_proyecto.documento, 
            tm_categoria.cat_nom AS producto,
            tm_subcategoria.cats_nom AS tipo,
            proyecto_gestionado.refProy AS referencia,
            clientes.client_rs AS cliente,
            tm_usuario.usu_correo
        FROM descripciones_proyecto 
            INNER JOIN proyecto_gestionado ON proyecto_gestionado.id = descripciones_proyecto.id_proyecto_gestionado
            INNER JOIN tm_categoria ON tm_categoria.cat_id = proyecto_gestionado.cat_id
            INNER JOIN proyecto_cantidad_servicios ON proyecto_cantidad_servicios.id = proyecto_gestionado.id_proyecto_cantidad_servicios
            INNER JOIN proyectos ON proyectos.proy_id = proyecto_cantidad_servicios.proy_id
            INNER JOIN clientes ON clientes.client_id = proyectos.client_id
            LEFT JOIN usuario_proyecto ON usuario_proyecto.id_proyecto_gestionado = proyecto_gestionado.id
            LEFT JOIN tm_usuario ON usuario_proyecto.usu_asignado = tm_usuario.usu_id
            INNER JOIN tm_subcategoria ON tm_subcategoria.cats_id=proyecto_gestionado.cats_id
            WHERE descripciones_proyecto.id_proyecto_gestionado = :id
            ORDER BY descripciones_proyecto.id DESC 
            LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc || empty($doc['documento'])) {
            $this->registrarEnvio($id_proyecto_gestionado, $pais_id == 1 ? SMTP_FROM_ARG : SMTP_FROM_INT, 'ERROR');
            return 'Sin documentos para enviar';
        }

        $id_descripciones_proyecto = $doc['id'];
        $carpeta  = $doc['carpeta_documentos_proy'];
        $archivos = array_filter(explode(',', $doc['documento']));
        $clave    = strtoupper(bin2hex(random_bytes(6)));

        $carpeta_zip = ZIP_PATH;
        if (!file_exists($carpeta_zip)) {
            mkdir($carpeta_zip, 0755, true);
        }
        $nombre_zip = 'informe_' . $id_proyecto_gestionado . '_' . date('Ymd_His') . '.zip';
        $ruta_zip   = $carpeta_zip . $nombre_zip;

        $zip = new ZipArchive();
        $resultado = $zip->open($ruta_zip, ZipArchive::CREATE);

        if ($resultado !== true) {
            return 'Error abriendo ZIP: ' . $resultado;
        }

        $archivos_encontrados = 0;
        foreach ($archivos as $archivo) {
            $ruta_archivo = BASE_PATH . "View/Home/Public/Uploads/Proyectos/" . $carpeta . "/" . trim($archivo);
            if (file_exists($ruta_archivo)) {
                $nombre_archivo = trim($archivo);
                $zip->addFile($ruta_archivo, $nombre_archivo);
                $resultado_enc = $zip->setEncryptionName($nombre_archivo, 1, $clave); // 1 = EM_DEFLATED

                if ($resultado_enc === false) {
                    error_log("Error encriptando {$nombre_archivo}: " . $zip->getStatusString());
                }
                $archivos_encontrados++;
            }
        }
        $zip->close();

        if ($archivos_encontrados === 0) {
            $this->registrarEnvio($id_proyecto_gestionado, $pais_id == 1 ? SMTP_FROM_ARG : SMTP_FROM_INT, 'ERROR');
            return 'No se encontraron archivos físicos en el servidor';
        }

        // CORREO 1 AL CLIENTE (con ZIP, sin clave en el texto)
        $mailCliente = new PHPMailer(true);
        try {
            if (SMTP_ENABLED === 'true') {
                $smtpConfig($mailCliente);
                $mailCliente->addAddress($correo_destino);
                $mailCliente->Subject = $pais_id == 1 ? $cliente . '|' . 'Informe del Servicio' . $producto . $tipo . 'ID: ' . $refProy  : $cliente . '|' . 'Informe del Servicio' . $producto . $tipo;
                $mailCliente->Body = "
        <p>Estimado/a cliente,</p>
               <p>
                    En el marco del servicio contratado <strong>{$doc['producto']} + {$doc['tipo']}</strong><strong> ID: " . ($doc['referencia'] ?: 'N/A') . "</strong> adjuntamos el informe correspondiente en formato ZIP protegido.<br><br> 
                    En otro correo le enviamos la clave para descifrar.<br><br>
                    Saludos,<br><br><br>
                    Equipo de Calidad y Procesos<br>
                    Delivery Services – Cybersecurity Solutions<br>
                    " . ($pais_id == 1 ? '<strong>Personal Tech</strong>' : '<strong>Ubiquo</strong>') . "
                </p>";
                $mailCliente->addAttachment($ruta_zip, $nombre_zip);
                $mailCliente->send();
            } else {
                throw new Exception('SMTP deshabilitado');
            }

            // CORREO 2 AL CLIENTE (con la clave)
            $mailClave = new PHPMailer(true);
            if (SMTP_ENABLED === 'true') {
                $smtpConfig($mailClave);
                $mailClave->addAddress($correo_destino);
                $mailClave->Subject = 'Clave de acceso - Documentos del Servicio ' . $doc['producto'];
                $mailClave->Body = "
                <p>Le compartimos la clave para abrir el archivo correspondiente a su servicio de <strong>{$doc['producto']}:</strong></p>
                <p style=\"font-size: 1.2rem; font-weight: bold; background: #f0f0f0; padding: 10px; border-radius: 5px;\">{$clave}</p>
                <p>Saludos.</p>";
                $mailClave->send();
            } else {
                throw new Exception('SMTP deshabilitado');
            }

            // CORREO 3 Y 4: ENVÍO DE ZIP Y CLAVE A MSSP-CALIDAD
            $correos_sectores = array_filter(array_map('trim', explode(',', MAIL_COPIA_SECTORES)));

            foreach ($correos_sectores as $correo_sector) {
                // ZIP a mssp-calidad
                $mailSectorZip = new PHPMailer(true);
                try {
                    if (SMTP_ENABLED === 'true') {
                        $smtpConfig($mailSectorZip);
                        $mailSectorZip->addAddress($correo_sector);
                        $mailSectorZip->Subject = '[Copia] Informe enviado al cliente - ' . $doc['producto'];
                        $mailSectorZip->Body = "
            <p>Se ha enviado documentación al cliente <strong>{$cliente}</strong> por el servicio <strong>{$producto} - {$tipo}</strong> ID: {$refProy}.</p>
            <p>Adjunto copia del ZIP enviado.</p>";
                        $mailSectorZip->addAttachment($ruta_zip, $nombre_zip);
                        $mailSectorZip->send();
                    }
                } catch (Exception $e) {
                    error_log("Error enviando ZIP a sector: " . $mailSectorZip->ErrorInfo);
                }

                // Clave a mssp-calidad
                $mailSectorClave = new PHPMailer(true);
                try {
                    if (SMTP_ENABLED === 'true') {
                        $smtpConfig($mailSectorClave);
                        $mailSectorClave->addAddress($correo_sector);
                        $mailSectorClave->Subject = '[Copia] Clave de acceso - ' . $doc['producto'];
                        $mailSectorClave->Body = "
            <p>Clave para abrir el ZIP enviado al cliente <strong>{$cliente}</strong>:</p>
            <p style=\"font-size: 1.2rem; font-weight: bold; background: #f0f0f0; padding: 10px; border-radius: 5px;\">{$clave}</p>";
                        $mailSectorClave->send();
                    }
                } catch (Exception $e) {
                    error_log("Error enviando clave a sector: " . $mailSectorClave->ErrorInfo);
                }
            }

            $id_ecc = $this->registrarEnvio($id_proyecto_gestionado, $pais_id == 1 ? SMTP_FROM_ARG : SMTP_FROM_INT, 'OK', $ruta_zip, $clave, $id_descripciones_proyecto, $correo_destino);
        } catch (Exception $e) {
            $id_ecc = $this->registrarEnvio($id_proyecto_gestionado, $pais_id == 1 ? SMTP_FROM_ARG : SMTP_FROM_INT, 'ERROR', $ruta_zip, $clave, $id_descripciones_proyecto, $correo_destino);
            foreach ($correos_copia as $correo_copia) {
                $this->registrarEnvioInterno(
                    $id_proyecto_gestionado,
                    $id_descripciones_proyecto,
                    trim($correo_copia),
                    'PENDIENTE',
                    'Envío al cliente fallido',
                    $id_ecc
                );
            }
            return 'ERROR - ' . 'Pais ID=' . $pais_id . ' - SMTP (cliente): ' . $mailCliente->ErrorInfo;
        }

        // COPIAS INTERNAS (sin ZIP, sin clave)
        foreach ($correos_copia as $correo_copia) {
            $correo_copia = trim($correo_copia);
            $mailCopia = new PHPMailer(true);
            try {
                if (SMTP_ENABLED === 'true') {
                    $smtpConfig($mailCopia);
                    $mailCopia->addAddress($correo_copia);
                    $mailCopia->Subject = 'Copia -'  . $doc['cliente'] . '| Informe del Servicio ' . $producto . ' - ' . $tipo . ' ID: ' . $refProy;
                    $mailCopia->Body = "
            <p>Estimado/a.</p>
            <p>
                Se enviaron los informes al cliente <strong>{$cliente}</strong> por el servicio <strong>{$producto} - {$tipo}</strong> ID: {$refProy} a los siguientes emails:<br>
                <strong>" . implode(', ', $correos_cliente_copia) . "</strong><br><br>
                Cualquier comentario por favor contactarse con Calidad-MSSP@personal.com.ar.<br><br>
                Saludos.<br><br>
                Equipo de Calidad y Procesos<br>
                Delivery Services – Cybersecurity Solutions
            </p>";
                    $mailCopia->send();
                } else {
                    throw new Exception('SMTP deshabilitado');
                }
                $this->registrarEnvioInterno($id_proyecto_gestionado, $id_descripciones_proyecto, $correo_copia, 'OK', '', $id_ecc);
            } catch (Exception $e) {
                $this->registrarEnvioInterno($id_proyecto_gestionado, $id_descripciones_proyecto, $correo_copia, 'ERROR', $mailCopia->ErrorInfo, $id_ecc);
            }
        }
        return [
            'status'               => 'OK',
            'clave'                => $clave,
            'zip'                  => $ruta_zip,
            'url_descarga'         => ZIP_URL . $nombre_zip,
            'archivos_encontrados' => $archivos_encontrados
        ];
    }

    private function registrarEnvio(int $id_proyecto_gestionado, string $smtp_user, string $status, string $ruta_zip = '', string $clave = '', ?int $id_descripciones_proyecto = null, string $correo_destino = ''): int
    {
        $conn = $this->get_conexion();
        $sql  = "INSERT INTO envio_correo_cliente (id_descripciones_proyecto, correo, id_proyecto_gestionado, smtp_user, usu_crea, sector_id, status_envio, ruta_comprimido, clave_comprimido, fech_crea) 
         VALUES (:id_desc, :correo, :id, :smtp_user, :usu, :sector, :status, :ruta, :clave, now())";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_desc', $id_descripciones_proyecto,  PDO::PARAM_INT);
        $stmt->bindValue(':smtp_user',  $smtp_user,               PDO::PARAM_STR);
        $stmt->bindValue(':correo',  $correo_destino,             PDO::PARAM_STR);
        $stmt->bindValue(':id',      $id_proyecto_gestionado,     PDO::PARAM_INT);
        $stmt->bindValue(':usu',     (int)$_SESSION['usu_id'],    PDO::PARAM_INT);
        $stmt->bindValue(':sector',  (int)$_SESSION['sector_id'], PDO::PARAM_INT);
        $stmt->bindValue(':status',  $status,                     PDO::PARAM_STR);
        $stmt->bindValue(':ruta',    $ruta_zip,                   PDO::PARAM_STR);
        $stmt->bindValue(':clave',   $clave,                      PDO::PARAM_STR);
        $stmt->execute();
        return (int)$conn->lastInsertId();
    }

    public function update_envio_correo($id, $status_envio)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE envio_correo_cliente SET status_envio=:status_envio, fech_actualizacion=now() WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":status_envio", $status_envio, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return "success";
        }
    }
    public function update_envio_correo_interno($id, $status_envio)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE envio_correo_interno SET status_envio=:status_envio, fech_actualizacion=now() WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":status_envio", $status_envio, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return "success";
        }
    }
}
