<?php
/**
 * Exporta datos de la API de Tasking a BigQuery (para Looker).
 *
 * Requisitos:
 *   composer require google/cloud-bigquery
 *   service-account.json en la misma carpeta (fuera del webroot, chmod 600)
 *   Service account con:
 *     - BigQuery Data Editor sobre el dataset
 *     - BigQuery Job User sobre el proyecto
 *
 * Uso:
 *   php tasking_to_bigquery.php
 *
 * Reemplaza la tabla completa en cada ejecución (WRITE_TRUNCATE).
 * Si la tabla no existe, la crea con el esquema detectado del JSON.
 */

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\BigQuery\BigQueryClient;

// ================== CONFIGURACIÓN ==================
$apiUrl      = 'https://tasking.telecom.com.ar/Api/prueba';
$apiKey      = 'key_e284b85509e45d38b4fa098c996dcdd0878f6ae6a9f293b635f960c006e96eef';
$projectId   = 'rare-daylight-403018';
$datasetId   = 'tasking';
$tableId     = 'pruebas';
$credentials = __DIR__ . '/service-account.json';
// ===================================================

function logMsg(string $msg): void
{
    echo "[" . date('Y-m-d H:i:s') . "] {$msg}\n";
}

// 1. Traer datos de la API de Tasking
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ["X-API-Key: {$apiKey}", "Accept: application/json"],
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_SSL_VERIFYPEER => false, // el servidor no confía en la CA corporativa (equivale a curl -k)
    CURLOPT_SSL_VERIFYHOST => 0,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    logMsg("Error API Tasking: HTTP {$httpCode} {$curlErr}");
    exit(1);
}

$rows = json_decode($response, true);

if (!is_array($rows) || empty($rows)) {
    logMsg("La API no devolvió filas");
    exit(1);
}

// 2. Convertir a NDJSON (una fila JSON por línea), formato que carga BigQuery
$ndjson = implode("\n", array_map(
    fn($r) => json_encode($r, JSON_UNESCAPED_UNICODE),
    $rows
));

// 3. Cargar en BigQuery (load job: funciona también en el sandbox gratuito)
try {
    $bigQuery = new BigQueryClient([
        'projectId'   => $projectId,
        'keyFilePath' => $credentials,
    ]);

    $table = $bigQuery->dataset($datasetId)->table($tableId);

    $loadConfig = $table->load($ndjson)
        ->sourceFormat('NEWLINE_DELIMITED_JSON')
        ->writeDisposition('WRITE_TRUNCATE')   // reemplaza todo el contenido
        ->createDisposition('CREATE_IF_NEEDED') // crea la tabla si no existe
        ->autodetect(true);                    // detecta el esquema desde el JSON

    $job  = $table->runJob($loadConfig); // espera a que termine
    $info = $job->info();

    if (!empty($info['status']['errorResult'])) {
        logMsg("Error BigQuery: " . json_encode($info['status']['errors'] ?? $info['status']['errorResult'], JSON_UNESCAPED_UNICODE));
        exit(1);
    }

    $filas = $info['statistics']['load']['outputRows'] ?? count($rows);
    logMsg("OK: {$filas} filas cargadas en {$projectId}.{$datasetId}.{$tableId}");
} catch (Throwable $e) {
    logMsg("Error BigQuery: " . $e->getMessage());
    exit(1);
}