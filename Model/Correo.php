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
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) $_ENV['SMTP_PORT'];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($_ENV['SMTP_USER'], 'Tasking');
            $mail->addAddress('mrgonzalez@personal.com.ar');
            $mail->isHTML(true);
            $mail->Subject = 'Proyecto finalizado - [CLIENTE] ' . $datos->cliente;
            $mail->Body = "
                <p>Estimados: <br><br>El presente proyecto se encuentra finalizado correctamente</p>
                <p><b>Título: </b> {$datos->titulo}</p>
                <p><b>Ref: </b> {$datos->refProy}</p>
                <p><b>Sector: </b> {$datos->sector}</p>
                <p><b>Producto: </b>{$datos->producto}</p>
                <p><b>Tipo: </b>{$datos->tipo}</p>
                <p><b>Usuarios: </b><br>{$datos->usuarios}</p>
                <p>Los informes se encuentran cargados en <strong>Tasking_stg</strong>.<br>Saludos.</p>
                ";   
            $mail->send();
            return true;
        } catch (Exception $e) {
            return $mail->ErrorInfo; // 👈 MOSTRAR ERROR REAL
        }
    }
}
