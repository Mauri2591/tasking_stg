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
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];      // usuario de red
            $mail->Password   = $_ENV['SMTP_PASS'];      // clave de red
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)$_ENV['SMTP_PORT'];
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('vulma-mssp@teco.com.ar', 'Tasking MSSP');
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
                <p><b>Usuarios:</b><br>{$datos->usuarios}</p>
                <p>
                <b>Envío al cliente:</b><br>
                Los informes fueron cargados en <strong>Tasking_stg</strong>.<br>
                Recuerde enviar el correo al cliente mediante Tasking_stg.
                En caso de falla, podrá realizar el envío por otro medio (por ejemplo, Outlook) y deberá registrarlo en Tasking_stg utilizando el botón <strong>Enviar por otro medio</strong>.
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

    public function enviarCorreoCliente(int $id_proyecto_gestionado, string $correo_destino, string $correos_copia_input = '')
    {
        $correos_copia = $this->getCorreosCopia($id_proyecto_gestionado, $correos_copia_input);

        // SMTP base
        $smtpConfig = function (PHPMailer $mail) {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
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
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->isHTML(true);
        };

        $datos    = $this->getDatosParaCorreo($id_proyecto_gestionado);
        $refProy  = $datos->refProy  ?: 'N/A';
        $producto = $datos->producto ?: 'N/A';
        $cliente  = $datos->cliente  ?: 'N/A';

        $conn = $this->get_conexion();
        $sql  = "SELECT descripciones_proyecto.id, 
            descripciones_proyecto.carpeta_documentos_proy, 
            descripciones_proyecto.documento, 
            tm_categoria.cat_nom AS producto,
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
        WHERE descripciones_proyecto.id_proyecto_gestionado = :id
        ORDER BY descripciones_proyecto.id DESC 
        LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc || empty($doc['documento'])) {
            $this->registrarEnvio($id_proyecto_gestionado, 'ERROR');
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
        $zip->open($ruta_zip, ZipArchive::CREATE);
        $zip->setPassword($clave);

        $archivos_encontrados = 0;
        foreach ($archivos as $archivo) {
            $ruta_archivo = BASE_PATH . "View/Home/Public/Uploads/Proyectos/" . $carpeta . "/" . trim($archivo);
            if (file_exists($ruta_archivo)) {
                $zip->addFile($ruta_archivo, trim($archivo));
                $zip->setEncryptionName(trim($archivo), ZipArchive::EM_AES_256);
                $archivos_encontrados++;
            }
        }
        $zip->close();

        if ($archivos_encontrados === 0) {
            $this->registrarEnvio($id_proyecto_gestionado, 'ERROR');
            return 'No se encontraron archivos físicos en el servidor';
        }

        // CORREO AL CLIENTE (con ZIP y clave)
        $mailCliente = new PHPMailer(true);
        try {
            if (SMTP_ENABLED === 'true') {
                $smtpConfig($mailCliente);
                $mailCliente->addAddress($correo_destino);
                $mailCliente->Subject = 'Documentos del Servicio ' . $doc['producto'] . ' - Personal Tech';
                $mailCliente->Body = "
        <p>Estimado/a cliente,</p>
        <p>Adjuntamos la documentación correspondiente a su servicio de <strong>{$doc['producto']}</strong> bajo la referencia <strong>" . ($doc['referencia'] ?: 'N/A') . "</strong> en formato ZIP protegido.</p>
        <p><strong>Clave para abrir el archivo:</strong> {$clave}</p>
        <p>Saludos.</p>";
                $mailCliente->addAttachment($ruta_zip, $nombre_zip);
                $mailCliente->send();
            } else {
                throw new Exception('SMTP deshabilitado');
            }
            $id_ecc = $this->registrarEnvio($id_proyecto_gestionado, 'OK', $ruta_zip, $clave, $id_descripciones_proyecto, $correo_destino);
        } catch (Exception $e) {
            $id_ecc = $this->registrarEnvio($id_proyecto_gestionado, 'ERROR', $ruta_zip, $clave, $id_descripciones_proyecto, $correo_destino);
            foreach ($correos_copia as $correo_copia) {
                $this->registrarEnvioInterno($id_proyecto_gestionado, $id_descripciones_proyecto, trim($correo_copia), 'PENDIENTE', 'Envío al cliente fallido', $id_ecc);
            }
            return 'ERROR SMTP (cliente): ' . $mailCliente->ErrorInfo;
        }

        // COPIAS INTERNAS (sin ZIP, sin clave)
        foreach ($correos_copia as $correo_copia) {
            $correo_copia = trim($correo_copia);
            $mailCopia = new PHPMailer(true);
            try {
                if (SMTP_ENABLED === 'true') {
                    $smtpConfig($mailCopia);
                    $mailCopia->addAddress($correo_copia);
                    $mailCopia->Subject = 'Copia - Documentos enviados al cliente: ' . $doc['cliente'];
                    $mailCopia->Body = "
            <p>Estimados,</p>
            <p>Se realizó el envío de documentación al cliente <strong>{$cliente}</strong> al email <strong>{$correo_destino}</strong> acorde al servicio <strong>{$producto}</strong> - bajo la referencia <strong>{$refProy}</strong>.</p>
            <p>Saludos.</p>";
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

    private function registrarEnvio(int $id_proyecto_gestionado, string $status, string $ruta_zip = '', string $clave = '', ?int $id_descripciones_proyecto = null, string $correo_destino = ''): int
    {
        $conn = $this->get_conexion();
        $sql  = "INSERT INTO envio_correo_cliente (id_descripciones_proyecto, correo, id_proyecto_gestionado, usu_crea, sector_id, status_envio, ruta_comprimido, clave_comprimido, fech_crea) 
         VALUES (:id_desc, :correo, :id, :usu, :sector, :status, :ruta, :clave, now())";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_desc', $id_descripciones_proyecto,  PDO::PARAM_INT);
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
