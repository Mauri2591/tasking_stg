<?php
require_once __DIR__ . "/../Config/Config.php";

$url          = 'https://www.cisa.gov/sites/default/files/feeds/known_exploited_vulnerabilities.json';
$dir_destino  = BASE_PATH . "Cache";
$destino      = $dir_destino . "/cisa_kev.json";
$tmp          = $destino . ".tmp";
$log_file     = $dir_destino . "/kev_update.log";

if (!is_dir($dir_destino)) {
    mkdir($dir_destino, 0755, true);
}

function log_kev(string $log_file, string $msg): void
{
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - $msg\n", FILE_APPEND);
}

function descargar_kev_curl(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_USERAGENT      => 'TaskingSTG/1.0',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $data      = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error     = curl_error($ch);
    curl_close($ch);

    return [
        'data'      => $data,
        'http_code' => $http_code,
        'error'     => $error,
    ];
}

$resultado = descargar_kev_curl($url);

if ($resultado['data'] === false || $resultado['error']) {
    log_kev($log_file, "ERROR cURL: " . $resultado['error'] . " (HTTP {$resultado['http_code']})");
    exit(1);
}

if ($resultado['http_code'] !== 200) {
    log_kev($log_file, "ERROR: HTTP {$resultado['http_code']} al descargar el catálogo KEV");
    exit(1);
}

$datos   = $resultado['data'];
$decoded = json_decode($datos, true);

if ($decoded === null || empty($decoded['vulnerabilities'])) {
    log_kev($log_file, "ERROR: JSON inválido o vacío, se mantiene el caché anterior");
    exit(1);
}

file_put_contents($tmp, $datos);
rename($tmp, $destino);

$total = count($decoded['vulnerabilities']);
log_kev($log_file, "OK: actualizado correctamente ($total entradas)");
exit(0);