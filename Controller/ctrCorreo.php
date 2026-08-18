<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Correo.php";
require_once __DIR__ . "/../Model/Auditoria.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        exit;

    case 'update_envio_correo':
        $datos = $correo->update_envio_correo($_POST['id'], $_POST['status_envio']);
        if ($datos == 'success') {
            echo json_encode(['status' => 'success']);
            http_response_code(200);
        } else {
            echo json_encode(['status' => 'error']);
            http_response_code(400);
        }
        exit;

    case 'diagnosticar':
        $diagnostico = [
            'timestamp' => date('Y-m-d H:i:s'),
            'server_info' => [
                'php_version' => phpversion(),
                'server_name' => $_SERVER['SERVER_NAME'] ?? 'N/A',
                'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
            ],
            'smtp_config' => [
                'SMTP_HOST' => SMTP_HOST,
                'SMTP_PORT' => SMTP_PORT,
                'SMTP_USER_ARG' => SMTP_USER_ARG,
                'SMTP_FROM_ARG' => SMTP_FROM_ARG,
                'SMTP_SECURE' => SMTP_SECURE,
                'SMTP_ENABLED' => SMTP_ENABLED,
            ],
            'connectivity_tests' => [],
            'smtp_auth_test' => null,
            'recomendaciones' => []
        ];

        // Test 1: Conectar con fsockopen
        $fp = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 5);
        if ($fp) {
            $diagnostico['connectivity_tests']['fsockopen'] = [
                'status' => 'exitoso',
                'mensaje' => 'Conexión establecida',
                'respuesta' => trim(fgets($fp, 512))
            ];
            fclose($fp);
        } else {
            $diagnostico['connectivity_tests']['fsockopen'] = [
                'status' => 'error',
                'error' => $errstr,
                'code' => $errno
            ];
            $diagnostico['recomendaciones'][] = "❌ No se puede conectar a " . SMTP_HOST . ":" . SMTP_PORT;
            $diagnostico['recomendaciones'][] = "Posibles causas: 1) Firewall bloqueando, 2) IP/Puerto incorrectos, 3) Servidor no disponible";
        }

        // Test 2: PHPMailer completo
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER_ARG;
            $mail->Password = SMTP_FROM_ARG_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int)SMTP_PORT;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->smtpConnect();
            $diagnostico['smtp_auth_test'] = [
                'status' => 'exitoso',
                'mensaje' => 'Conexión y autenticación SMTP exitosa ✅'
            ];
            $mail->smtpClose();
            $diagnostico['recomendaciones'][] = "✅ TODO FUNCIONA CORRECTAMENTE";

        } catch (Exception $e) {
            $diagnostico['smtp_auth_test'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];

            $msg = $e->getMessage();
            if (strpos($msg, 'authenticate') !== false || strpos($msg, 'Authenticate') !== false) {
                $diagnostico['recomendaciones'][] = "❌ ERROR DE AUTENTICACIÓN - Verifica usuario/contraseña";
            } elseif (strpos($msg, 'Failed to connect') !== false || strpos($msg, 'Connection refused') !== false) {
                $diagnostico['recomendaciones'][] = "❌ ERROR DE CONEXIÓN - Verifica firewall o puerto";
            }
        }

        echo json_encode($diagnostico, JSON_PRETTY_PRINT);
        exit;

    default:
        echo json_encode([
            'status' => 'ERROR',
            'error'  => 'Acción no válida'
        ]);
        exit;
}
