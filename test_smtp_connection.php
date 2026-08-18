<?php
/**
 * Script de diagnóstico SMTP detallado
 * Coloca en la raíz del proyecto y accede a:
 * https://tasking.telecom.com.ar/test_smtp_connection.php
 */

require_once __DIR__ . "/Config/Config.php";

header('Content-Type: application/json; charset=utf-8');

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

// Test 1: Conectar con fsockopen (bajo nivel)
echo "<!-- Test 1: Conectar con fsockopen a " . SMTP_HOST . ":" . SMTP_PORT . " -->\n";
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
    $diagnostico['recomendaciones'][] = "   No se puede conectar a " . SMTP_HOST . ":" . SMTP_PORT;
    $diagnostico['recomendaciones'][] = "   Posibles causas:";
    $diagnostico['recomendaciones'][] = "   1. Firewall bloqueando la conexión";
    $diagnostico['recomendaciones'][] = "   2. IP/Puerto incorrectos";
    $diagnostico['recomendaciones'][] = "   3. Servidor SMTP no está disponible";
}

// Test 2: PHPMailer completo
try {
    require_once __DIR__ . "/vendor/autoload.php";
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer(true);

    // Habilitar debug para ver qué pasa
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        global $diagnostico;
        if (!isset($diagnostico['debug_output'])) {
            $diagnostico['debug_output'] = [];
        }
        $diagnostico['debug_output'][] = $str;
    };

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

    // Intentar conexión
    $mail->smtpConnect();
    $diagnostico['smtp_auth_test'] = [
        'status' => 'exitoso',
        'mensaje' => 'Conexión y autenticación SMTP exitosa'
    ];
    $mail->smtpClose();

} catch (Exception $e) {
    $diagnostico['smtp_auth_test'] = [
        'status' => 'error',
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ];

    // Análisis del error
    $msg = $e->getMessage();
    if (strpos($msg, 'authenticate') !== false || strpos($msg, 'Authenticate') !== false) {
        $diagnostico['recomendaciones'][] = "❌ ERROR DE AUTENTICACIÓN";
        $diagnostico['recomendaciones'][] = "   Verifica:";
        $diagnostico['recomendaciones'][] = "   1. Usuario CORRECTO: " . SMTP_USER_ARG;
        $diagnostico['recomendaciones'][] = "   2. Contraseña CORRECTA: " . SMTP_FROM_ARG_PASS;
    } elseif (strpos($msg, 'Failed to connect') !== false || strpos($msg, 'Connection refused') !== false) {
        $diagnostico['recomendaciones'][] = "❌ ERROR DE CONEXIÓN";
        $diagnostico['recomendaciones'][] = "   Verifica:";
        $diagnostico['recomendaciones'][] = "   1. Host CORRECTO: " . SMTP_HOST;
        $diagnostico['recomendaciones'][] = "   2. Puerto CORRECTO: " . SMTP_PORT;
        $diagnostico['recomendaciones'][] = "   3. Firewall no bloquee puerto " . SMTP_PORT;
    } elseif (strpos($msg, 'STARTTLS') !== false) {
        $diagnostico['recomendaciones'][] = "❌ ERROR CON STARTTLS";
        $diagnostico['recomendaciones'][] = "   Prueba cambiar SMTP_SECURE a 'tls' o verificar configuración";
    }
}

// Test 3: Verificar DNS
if (function_exists('gethostbyname')) {
    $ip = @gethostbyname(SMTP_HOST);
    $diagnostico['dns_test'] = [
        'hostname' => SMTP_HOST,
        'resolved_ip' => $ip,
        'nota' => 'Si SMTP_HOST es una IP, este test no es relevante'
    ];
}

// Resumen
if (empty($diagnostico['recomendaciones'])) {
    $diagnostico['recomendaciones'][] = "✅ TODO PARECE ESTAR BIEN. Si aún así no funciona, contacta al equipo de BISO";
}

echo json_encode($diagnostico, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
