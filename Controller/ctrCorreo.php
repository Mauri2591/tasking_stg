<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Correo.php";
require_once __DIR__ . "/../Model/Auditoria.php";

$correo = new Correo();
$auditoria = new Auditoria();

header('Content-Type: application/json; charset=utf-8');
$case = $_GET['correo'] ?? '';

switch ($case) {
    case 'enviar':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            echo json_encode([
                'status' => 'ERROR',
                'error'  => 'ID inválido'
            ]);
            exit;
        }
        $result = $correo->enviarCorreoProyectoFinalizado($id);
        echo json_encode([
            'status' => $result === true ? 'OK' : 'ERROR',
            'error'  => $result === true ? null : $result
        ]);
        exit;

    case 'enviar_correo_cliente':
        $id_proyecto_gestionado = isset($_POST['id_proyecto_gestionado']) ? (int)$_POST['id_proyecto_gestionado'] : 0;
        $correo_destino         = $_POST['correo_destino'] ?? '';

        if ($id_proyecto_gestionado <= 0) {
            echo json_encode(['status' => 'ERROR', 'error' => 'ID inválido']);
            exit;
        }

        if (!filter_var($correo_destino, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'ERROR', 'error' => 'Correo destino inválido']);
            exit;
        }

        $result = $correo->enviarCorreoCliente($id_proyecto_gestionado, $correo_destino, $_POST['correos_copia_input'] ?? '');

        if (is_array($result)) {
            $auditoria->insert_audit_estados_proyecto($_POST['id_proyecto_gestionado'], 21, $_SESSION['usu_id'], $_SESSION['sector_id']);
            echo json_encode($result);
        } else {
            // Retornó un string de error
            echo json_encode(['status' => 'ERROR', 'error' => $result]);
        }
        exit;

    case 'update_envio_correo_interno':
        $datos = $correo->update_envio_correo_interno($_POST['id'], $_POST['status_envio']);
        if ($datos == 'success') {
            echo json_encode(['status' => 'success']);
            http_response_code(200);
        } else {
            echo json_encode(['status' => 'error']);
            http_response_code(400);
        }
        break;

    case 'update_envio_correo':
        $datos = $correo->update_envio_correo($_POST['id'], $_POST['status_envio']);
        if ($datos == 'success') {
            echo json_encode(['status' => 'success']);
            http_response_code(200);
        } else {
            echo json_encode(['status' => 'error']);
            http_response_code(400);
        }
        break;

    default:
        echo json_encode([
            'status' => 'ERROR',
            'error'  => 'Acción no válida'
        ]);
        exit;
}
