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
            // FROM correcto
            $mail->setFrom('vulma-mssp@teco.com.ar', 'Tasking MSSP');
            $mail->addAddress('mssp-calidad@personal.com.ar');
            $mail->isHTML(true);
            $mail->Subject = 'Proyecto finalizado - [CLIENTE] ' . $datos->cliente;
            $mail->Body = "<p>Estimados,<br><br>
            El presente proyecto se encuentra finalizado correctamente.</p>
            <p><b>Título:</b> {$datos->titulo}</p>
            <p><b>Ref:</b> {$datos->refProy}</p>
            <p><b>Sector:</b> {$datos->sector}</p>
            <p><b>Producto:</b> {$datos->producto}</p>
            <p><b>Tipo:</b> {$datos->tipo}</p>
            <p><b>Usuarios:</b><br>{$datos->usuarios}</p>
            <p>Los informes se encuentran cargados en <strong>Tasking_stg</strong>.</p>
            <br>
            <p>Saludos.</p>";
            $mail->send();
            return true;
        } catch (Exception $e) {
            return 'ERROR SMTP: ' . $mail->ErrorInfo;
        }
    }

    public function enviarCorreoCliente(int $id_proyecto_gestionado, string $correo_destino)
    {
        $conn = $this->get_conexion();
        $sql  = "SELECT id, carpeta_documentos_proy, documento 
             FROM descripciones_proyecto 
             WHERE id_proyecto_gestionado = :id 
             ORDER BY id DESC LIMIT 1";
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

        $this->registrarEnvio($id_proyecto_gestionado, 'OK', $ruta_zip, $clave, $id_descripciones_proyecto);

        return [
            'status'               => 'OK_TEST',
            'clave'                => $clave,
            'zip'                  => $ruta_zip,
            'url_descarga'         => ZIP_URL . $nombre_zip,
            'archivos_encontrados' => $archivos_encontrados
        ];
    }

    private function registrarEnvio(int $id_proyecto_gestionado, string $status, string $ruta_zip = '', string $clave = '', ?int $id_descripciones_proyecto = null): void
    {
        $conn = $this->get_conexion();
        $sql  = "INSERT INTO envio_correo_cliente (id_descripciones_proyecto, id_proyecto_gestionado, usu_crea, sector_id, status_envio, ruta_comprimido, clave_comprimido, fech_crea) 
             VALUES (:id_desc, :id, :usu, :sector, :status, :ruta, :clave, now())";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_desc', $id_descripciones_proyecto,  PDO::PARAM_INT);
        $stmt->bindValue(':id',      $id_proyecto_gestionado,     PDO::PARAM_INT);
        $stmt->bindValue(':usu',     (int)$_SESSION['usu_id'],    PDO::PARAM_INT);
        $stmt->bindValue(':sector',  (int)$_SESSION['sector_id'], PDO::PARAM_INT);
        $stmt->bindValue(':status',  $status,                     PDO::PARAM_STR);
        $stmt->bindValue(':ruta',    $ruta_zip,                   PDO::PARAM_STR);
        $stmt->bindValue(':clave',   $clave,                      PDO::PARAM_STR);
        $stmt->execute();
    }
}
