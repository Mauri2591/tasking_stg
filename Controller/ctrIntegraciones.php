<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Integraciones.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";
require_once __DIR__ . "/../Model/Proyectos.php";
require_once __DIR__ . "/../Model/Clases/ExtractorDocumentos.php";
require_once __DIR__ . "/../Model/Clases/CisaKevChecker.php";

$proyecto = new Proyectos();
$integracion = new Integraciones();


$min_palabras = 10;
$medium_palabras = 30;
$max_palabras = 50;


switch ($_GET['case']) {

    case 'get_herramientas':
        $datos = $integracion->get_herramientas();
        $htmlOption = '';
        foreach ($datos as $key => $val) {
            $htmlOption .= '<option value=' . $val['id'] . '>' . $val['herramienta'] . '</option>';
        }
        echo $htmlOption;
        break;

    case 'get_api_key':
        if (empty($_POST['sector_id']) || empty($_POST['id_herramienta'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "Datos incompletos"
            ]);
            exit;
        }

        $sector_id = (int) $_POST['sector_id'];

        $tieneActiva = $integracion->existe_api_key_activa_x_sector($sector_id);
        if ($tieneActiva) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "El sector ya posee una API Key activa"
            ]);
            exit;
        }
        $api_key_plana = 'key_' . bin2hex(random_bytes(32));

        $api_key_plana = 'key_' . bin2hex(random_bytes(32));
        $api_key_cifrada = Openssl::set_ssl_encrypt($api_key_plana);

        $ok = $integracion->crear_api_key(
            $api_key_cifrada,
            $sector_id,
            (int) $_POST['id_herramienta'],
            $_SESSION['usu_id']
        );
        if ($ok) {
            http_response_code(201);
            echo json_encode([
                "status"  => "success",
                "api_key" => $api_key_plana,
                "mensaje" => "API Key creada correctamente"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo crear la API Key"
            ]);
        }
        exit;


    case 'get_api_keys':
        $datos = $integracion->get_api_keys();
        $data = array();
        $colores_prioridad = array("BAJO" => "badge border border-success text-success", "MEDIO" => "badge border border-warning text-warning", "ALTO" => "badge border border-danger text-danger");
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = Openssl::get_ssl_decrypt($row['api_key']);
            $sub_array[] = $row['herramienta'];
            $sub_array[] = $row['sector'];
            $sub_array[] = $row['usu_crea'];
            $sub_array[] = '<span title="Eliminar Api Key" type="button" onclick=inactivarApiKey(' . $row['id'] . ')><i class=" ri-delete-bin-5-fill text-danger fs-16"></i></span>';
            $data[] = $sub_array;
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'inhabilitar_api_keys':
        $datos = $integracion->inhabilitar_api_keys($_POST['id']);
        if ($datos > 0) {
            echo json_encode(["status" => "success", "mensaje" => "Inhabilitado correctamente"]);
            http_response_code(201);
            exit;
        } else {
            echo json_encode(["status" => "error", "mensaje" => "No se pudo inhabilitar"]);
            http_response_code(401);
            exit;
        }
        break;

    case 'get_models':
        /*En este endpoint veo la documentacion: https://chat.genia-dev.click/api/v1/docs y
        https://chat.genia-dev.click/api/v1/openapi.json*/
        session_write_close();
        $api_key = trim($_POST['api_key'] ?? '');
        if (!$api_key) {
            echo json_encode(['error' => 'Falta api_key']);
            exit;
        }

        $ch = curl_init(BASE_GENAI . '/api/v1/models/available?page_size=100');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $api_key],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data    = json_decode($raw, true);
        $modelos = [];

        foreach ($data['data'] ?? [] as $m) {
            if (($m['model_info']['mode'] ?? '') === 'chat_completion') {
                $modelos[] = [
                    'nombre'      => $m['model_name'],
                    'modo'        => $m['model_info']['mode'] ?? '-',
                    'descripcion' => $m['description'] ?? '-',
                    'input_cost'  => $m['model_info']['input_cost_per_token'] ?? '-',
                    'output_cost' => $m['model_info']['output_cost_per_token'] ?? '-',
                ];
            }
        }
        echo json_encode(['http_code' => $code, 'modelos' => $modelos]);
        exit;

    case 'chat':
        session_write_close();
        $api_key = trim($_POST['api_key'] ?? '');
        $modelo  = trim($_POST['modelo'] ?? '');
        $prompt  = trim($_POST['prompt'] ?? '');

        if (!$api_key || !$modelo || !$prompt) {
            echo json_encode(['error' => 'Faltan parámetros']);
            exit;
        }

        $ch = curl_init(BASE_GENAI . '/api/v1/chat');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'model'          => $modelo,
                'messages'       => [['role' => 'user', 'content' => $prompt, 'type' => 'text']],
                'stream'         => true,
                'enable_history' => true,
                'with_details'   => false,
                'max_tokens'     => 7552,
                'temperature'    => 0.2,
                'files'          => [],
                'chat_id'        => null,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key,
            ],
            CURLOPT_TIMEOUT => 60,
        ]);

        $raw       = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $texto_final = '';
        $chat_id     = '';
        foreach (explode("\n", $raw) as $linea) {
            $linea = trim($linea);
            if (!str_starts_with($linea, 'data: ')) continue;
            $chunk = json_decode(substr($linea, 6), true);
            if (!$chunk) continue;
            if (($chunk['data']['finish_reason'] ?? null) === false) {
                $texto_final .= $chunk['data']['content'] ?? '';
            } else {
                $chat_id = $chunk['data']['chat_id'] ?? '';
            }
        }
        echo json_encode([
            'http_code' => $http_code,
            'respuesta' => $texto_final,
            'chat_id'   => $chat_id,
        ]);
        exit;

    case 'test_extraer_texto':
        session_write_close();
        $id     = (int) ($_POST['id'] ?? 0);
        $indice = (int) ($_POST['indice'] ?? 0);
        if (!$id) {
            echo json_encode(['error' => 'Falta id']);
            exit;
        }
        $info = $proyecto->get_archivos_por_id_descripciones_proyecto($id);
        if (!$info || empty($info['carpeta_documentos_proy']) || empty($info['documento'])) {
            echo json_encode(['error' => 'No se encontraron archivos para ese id']);
            exit;
        }
        $archivos = array_values(array_filter(array_map('trim', explode(',', $info['documento']))));
        $carpeta  = $info['carpeta_documentos_proy'];
        $rutas = [];
        foreach ($archivos as $archivo) {
            $rutas[] = BASE_PATH . "View/Home/Public/Uploads/Proyectos/" . $carpeta . "/" . $archivo;
        }
        $ruta = $rutas[$indice] ?? null;
        if (!$ruta) {
            echo json_encode(['error' => 'Índice fuera de rango', 'todos_los_archivos' => $rutas]);
            exit;
        }
        $resultado = ExtractorDocumentos::extraer($ruta);
        echo json_encode([
            'archivo'        => basename($ruta),
            'ok'             => $resultado['ok'],
            'error'          => $resultado['error'],
            'longitud_texto' => strlen($resultado['texto']),
            'texto_preview'  => mb_substr($resultado['texto'], 0, 1000),
        ]);
        exit;

    case 'analisis_resumen_documentos_eh':
        session_write_close();
        set_time_limit(0); // sin límite de tiempo para este endpoint

        $JWT_PRUEBA = JWT_API_GENIA;

        $id     = (int) ($_POST['id'] ?? 0);
        $modelo = trim($_POST['modelo'] ?? 'Gemini 2.5 Flash');
        $modo   = trim($_POST['modo'] ?? 'default'); // 'default' | 'personalizado'
        $prompt_personalizado = trim($_POST['prompt_personalizado'] ?? '');

        if (!$id) {
            echo json_encode(['error' => 'Falta id']);
            exit;
        }

        if ($modo === 'personalizado' && $prompt_personalizado === '') {
            echo json_encode(['error' => 'Falta el prompt personalizado']);
            exit;
        }

        $secciones = [
            'RESUMEN'                    => $min_palabras,
            'HALLAZGOS'                  => $max_palabras,
            'CONCLUSIONES'               => $medium_palabras,
            'TRADUCCIÓN'                 => $medium_palabras,
        ];

        $info = $proyecto->get_archivos_por_id_descripciones_proyecto($id);

        if (!$info || empty($info['carpeta_documentos_proy']) || empty($info['documento'])) {
            echo json_encode(['error' => 'No se encontraron archivos para ese id']);
            exit;
        }

        $tipo_servicio = $info['categoria'] ?? 'Ethical Hacking';

        $archivos = array_values(array_filter(array_map('trim', explode(',', $info['documento']))));
        $carpeta  = $info['carpeta_documentos_proy'];

        $detalle_secciones = '';
        foreach ($secciones as $nombre => $cant_palabras) {
            $detalle_secciones .= "- $nombre: aproximadamente $cant_palabras palabras\n";
        }

        // Instrucción base: la fija del sistema (modo default) o la que escribió el usuario (modo personalizado)
        if ($modo === 'personalizado') {
            $instruccion_base = "Actuá como analista de ciberseguridad que realizó el siguiente servicio: $tipo_servicio. "
                . "A continuación tenés el contenido completo de un documento de un proyecto de $tipo_servicio.\n\n"
                . "Instrucciones específicas del usuario sobre qué generar:\n"
                . $prompt_personalizado . "\n\n";
        } else {
            $instruccion_base = "Actuá como analista de ciberseguridad que realizó el siguiente servicio: $tipo_servicio. "
                . "A continuación tenés el contenido completo de un documento de un proyecto de $tipo_servicio.\n\n"
                . "El documento contiene un informe técnico. Generá un resumen enfocado ÚNICAMENTE en estas secciones, "
                . "respetando la cantidad de palabras indicada para cada una:\n"
                . $detalle_secciones . "\n"
                . "No incluyas ninguna otra sección además de las listadas arriba, aunque el documento original tenga "
                . "secciones como Objetivos, Alcance, Recomendaciones Generales, Actividades Realizadas o Metodología "
                . "— esas secciones deben omitirse completamente del resultado, sin excepción, sin mencionarlas ni "
                . "resumirlas.\n\n"
                . "Para la sección HALLAZGOS, además de describir las vulnerabilidades encontradas, es OBLIGATORIO que "
                . "señales lo siguiente para cada hallazgo relevante (no es opcional, si el documento lo menciona tenés "
                . "que incluirlo):\n"
                . "- Si tiene exploits públicos conocidos o es activamente explotada. Si más abajo se incluye un bloque "
                . "de \"CONTEXTO VERIFICADO\" con datos del catálogo CISA KEV, usalo como fuente PRIORITARIA y confiable "
                . "por sobre tu conocimiento general — es información oficial y actualizada, y las coincidencias "
                . "\"aproximadas\" deben mencionarse como tal, sin afirmar explotación activa confirmada.\n"
                . "- CVE asociado a cada hallazgo, si está disponible.\n"
                . "OBLIGATORIO: cubrí TODOS los hallazgos del documento, incluyendo los de severidad Informativa o "
                . "Baja — no omitas ninguno aunque parezca menor. Si el documento tiene 5 hallazgos, el resumen debe "
                . "dar cuenta de los 5, aunque sea brevemente agrupados (ej: \"además, 2 hallazgos informativos sobre "
                . "servicios expuestos\"). Nunca dejes una vulnerabilidad del documento sin mencionar.\n"
                . "PROHIBICIÓN ESTRICTA sobre CVEs: NUNCA inventes, sugieras o \"adivines\" un CVE que no esté "
                . "literalmente escrito en el documento o en el bloque CONTEXTO VERIFICADO. Si el documento no "
                . "menciona un CVE para un hallazgo (por ejemplo, si es una planilla sin columna de CVE), escribí "
                . "\"CVE no especificado en el documento\" o simplemente omitilo — NUNCA propongas un CVE \"posible\" "
                . "o \"probable\" basado en tu conocimiento general, aunque exista en la realidad. Citar un CVE "
                . "incorrecto en un informe de seguridad es un error grave que puede llevar a remediar la "
                . "vulnerabilidad equivocada.\n"
                . "- Si el documento menciona disponibilidad de parche (o ausencia del mismo) y/o la antigüedad del "
                . "CVE, repetilo en el resumen — es información clave para priorizar.\n"
                . "PROHIBICIÓN: no afirmes que existe o no existe parche disponible si el documento no lo menciona "
                . "explícitamente (por ejemplo, una planilla sin columna de \"Solución\"). En ese caso omitilo, no "
                . "lo completes con tu conocimiento general — la misma regla que aplica a los CVEs aplica acá.\n"
                . "- Cuáles representan mayor riesgo real de explotación (no solo por score CVSS, sino por facilidad/madurez "
                . "del exploit y si está en el catálogo KEV).\n"
                . "- Ordená los hallazgos de mayor a menor severidad, usando ÚNICAMENTE las categorías de severidad que "
                . "figuren en el propio documento (ej: si el documento solo clasifica como Alta/Media/Baja/Informativa, "
                . "NO inventes ni menciones la categoría \"Crítica\" salvo que el documento la use explícitamente para "
                . "ese hallazgo).\n\n"
                . "PROHIBICIÓN ESTRICTA: no uses la palabra \"crítica\", \"crítico\", \"críticas\" o \"críticos\" en NINGUNA "
                . "parte del resumen (ni en RESUMEN, ni en HALLAZGOS, ni en CONCLUSIONES), salvo que esa palabra exacta "
                . "aparezca como clasificación de severidad de ese hallazgo específico en el documento original. Usá "
                . "\"grave\", \"de alto impacto\" o \"de alta severidad\" como alternativas si necesitás enfatizar gravedad.\n\n"
                . "Si alguna de las secciones pedidas (RESUMEN, HALLAZGOS, CONCLUSIONES) no está presente en el "
                . "documento, omitila sin mencionarla.\n\n"
                . "Para la sección TRADUCCIÓN (a diferencia de RESUMEN, HALLAZGOS y CONCLUSIONES, esta sección es "
                . "CONDICIONAL): los documentos analizados suelen provenir de un escáner en inglés que pasa por una "
                . "herramienta de traducción automática antes de llegar a vos. Esta sección sirve para detectar dos "
                . "tipos de problemas en el texto en español del documento:\n"
                . "1) Palabras YA TRADUCIDAS AL ESPAÑOL que usan vocabulario neutro o de España en vez del español "
                . "técnico habitual en Argentina (ejemplo real: \"llaves\" en vez de \"claves\", para \"keys\" de "
                . "autenticación).\n"
                . "2) Frases sin sentido o gramaticalmente rotas que parecen errores de la herramienta de traducción "
                . "u OCR (ejemplo real: \"Otras ostras realizadas\" en vez de \"Otras actividades realizadas\") — son "
                . "errores de transcripción, no necesariamente de dialecto, pero igual interesa que el usuario los "
                . "detecte para revisar el documento original.\n"
                . "IMPORTANTE - NO es el objetivo de esta sección: palabras o términos que quedaron SIN TRADUCIR, "
                . "es decir que todavía están en inglés en el documento (ejemplos: \"ciphers\", \"backdoor\", \"patch\", "
                . "nombres de productos o protocolos). Esos términos en inglés NO deben aparecer en esta sección bajo "
                . "ninguna circunstancia, ya estén o no traducidos al español en otra parte del documento — son "
                . "terminología técnica válida y se dejan como están.\n"
                . "Formato: \"palabra o frase usada\" → sugerencia corregida, con una brevísima aclaración de por qué. "
                . "Si NO encontrás ningún problema de los dos tipos descriptos arriba, NO incluyas la sección "
                . "TRADUCCIÓN en absoluto — omitila completamente, no escribas que no se encontraron problemas, y no "
                . "la cuentes como una sección faltante.\n\n"
                . "Formato de salida: usá el nombre de cada sección en mayúsculas como encabezado, seguido del texto "
                . "correspondiente, sin numeración ni viñetas adicionales.\n\n";
        }
        $kev = new CisaKevChecker(BASE_PATH . "Cache/cisa_kev.json");

        $resultados = [];

        foreach ($archivos as $archivo) {
            $ruta = BASE_PATH . "View/Home/Public/Uploads/Proyectos/" . $carpeta . "/" . $archivo;
            $ext  = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

            $contexto_verificado = '';

            // Contexto KEV: se agrega automático en ambos modos, default y personalizado
            if (in_array($ext, ['xlsx', 'xls'], true)) {
                $vulns = ExtractorDocumentos::extraerVulnerabilidadesXlsx($ruta);
                if ($vulns['ok'] && !empty($vulns['vulnerabilidades'])) {
                    $contexto_verificado = $kev->generarContextoVerificado($vulns['vulnerabilidades']);
                }
            }

            $extraido = ExtractorDocumentos::extraer($ruta);

            if (!$extraido['ok']) {
                $resultados[] = [
                    'documento' => $archivo,
                    'resumen'   => null,
                    'error'     => $extraido['error'],
                ];
                continue;
            }

            // Para documentos narrativos (docx/pdf), buscamos CVEs explícitos mencionados en el texto
            if ($contexto_verificado === '' && !in_array($ext, ['xlsx', 'xls'], true)) {
                $analisis = $kev->analizarTexto($extraido['texto']);
                if (!empty($analisis['confirmados'])) {
                    $contexto_verificado = "=== CONTEXTO VERIFICADO: Catálogo CISA KEV ===\n";
                    $contexto_verificado .= "Los siguientes CVEs mencionados en el documento están confirmados en "
                        . "el catálogo oficial de CISA como vulnerabilidades EXPLOTADAS ACTIVAMENTE:\n\n";
                    foreach ($analisis['confirmados'] as $c) {
                        $ransomware = $c['knownRansomwareCampaignUse'] ?? 'Unknown';
                        $contexto_verificado .= sprintf(
                            "- %s: %s | Uso conocido en campañas de ransomware: %s\n",
                            $c['cveID'],
                            $c['vulnerabilityName'],
                            $ransomware
                        );
                    }
                    $contexto_verificado .= "=== FIN CONTEXTO VERIFICADO ===\n\n";
                }
            }

            $prompt = $instruccion_base
                . $contexto_verificado
                . "=== Contenido del documento ===\n"
                . $extraido['texto'];

            $ch = curl_init(BASE_GENAI . '/api/v1/chat');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode([
                    'model'          => $modelo,
                    'messages'       => [['role' => 'user', 'content' => $prompt, 'type' => 'text']],
                    'stream'         => true,
                    'enable_history' => false,
                    'with_details'   => false,
                    'max_tokens'     => 7552,
                    'temperature'    => 0.2,
                    'files'          => [],
                    'chat_id'        => null,
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $JWT_PRUEBA,
                ],
                CURLOPT_TIMEOUT => 90,
            ]);

            $raw = curl_exec($ch);
            $http_code_doc = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_err = curl_error($ch);
            curl_close($ch);

            $texto_final = '';
            foreach (explode("\n", $raw) as $linea) {
                $linea = trim($linea);
                if (!str_starts_with($linea, 'data: ')) continue;
                $chunk = json_decode(substr($linea, 6), true);
                if (!$chunk) continue;
                if (($chunk['data']['finish_reason'] ?? null) === false) {
                    $texto_final .= $chunk['data']['content'] ?? '';
                }
            }

            $resultados[] = [
                'documento' => $archivo,
                'resumen'   => $texto_final,
                'error'     => null,
                // --- TEMPORAL para diagnóstico, sacar después ---
                'debug_http_code' => $http_code_doc,
                'debug_curl_error' => $curl_err,
                'debug_raw_preview' => mb_substr($raw, 0, 800),
                'debug_prompt_length' => mb_strlen($prompt),
            ];

            if ($texto_final !== '') {
                $integracion->insertar_resumen_documento_ia(
                    $id,
                    $archivo,
                    $texto_final,
                    $modelo,
                    $_SESSION['usu_id'],
                    $modo,
                    $modo === 'personalizado' ? $prompt_personalizado : null
                );
            }
        }

        echo json_encode([
            'modelo_usado'  => $modelo,
            'modo'          => $modo,
            'tipo_servicio' => $tipo_servicio,
            'resultados'    => $resultados,
        ]);
        exit;

    case 'get_resumenes_documentos_ia':
        session_write_close();
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['error' => 'Falta id']);
            exit;
        }
        $resumenes = $integracion->obtener_resumenes_documento_ia($id);
        echo json_encode([
            'existen'   => count($resumenes) > 0,
            'resumenes' => $resumenes,
        ]);
        exit;


    case 'test_kev_checker':
        session_write_close();

        require_once __DIR__ . "/../Model/Clases/CisaKevChecker.php";

        $kev = new CisaKevChecker(BASE_PATH . "Cache/cisa_kev.json");

        // Probamos con los nombres reales de tu Excel de vulnerabilidades
        $nombres_prueba = [
            'Redis Server Accessible Without Authentication',
            'Redis Server Remote Code Execution (RCE) Vulnerability',
            'Drupal Core Gadget chain Vulnerability (SA-CORE-2024-007)',
            'Grafana Authentication Bypass Vulnerability',
            'OpenSSH Remote Code Execution (RCE) Vulnerability in its forwarded ssh-agent',
        ];

        $resultados = [];
        foreach ($nombres_prueba as $nombre) {
            $matches = $kev->buscarPorNombre($nombre);
            $resultados[] = [
                'nombre_buscado' => $nombre,
                'matches_encontrados' => count($matches),
                'candidatos' => array_map(fn($m) => [
                    'cve' => $m['cveID'],
                    'nombre_kev' => $m['vulnerabilityName'],
                    'vendor' => $m['vendorProject'],
                ], $matches),
            ];
        }

        echo json_encode([
            'total_entradas_catalogo' => $kev->totalEntradas(),
            'resultados' => $resultados,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;

    case 'test_extraer_vulns_xlsx':
        session_write_close();

        $id     = (int) ($_POST['id'] ?? 0);
        $indice = (int) ($_POST['indice'] ?? 4); // 4 = el xlsx en tu proyecto MAINCAL

        if (!$id) {
            echo json_encode(['error' => 'Falta id']);
            exit;
        }

        $info = $proyecto->get_archivos_por_id_descripciones_proyecto($id);

        if (!$info || empty($info['carpeta_documentos_proy']) || empty($info['documento'])) {
            echo json_encode(['error' => 'No se encontraron archivos para ese id']);
            exit;
        }

        $archivos = array_values(array_filter(array_map('trim', explode(',', $info['documento']))));
        $carpeta  = $info['carpeta_documentos_proy'];

        $ruta = isset($archivos[$indice])
            ? BASE_PATH . "View/Home/Public/Uploads/Proyectos/" . $carpeta . "/" . $archivos[$indice]
            : null;

        if (!$ruta) {
            echo json_encode(['error' => 'Índice fuera de rango', 'todos_los_archivos' => $archivos]);
            exit;
        }

        $resultado = ExtractorDocumentos::extraerVulnerabilidadesXlsx($ruta);

        echo json_encode([
            'archivo' => basename($ruta),
            'ok'      => $resultado['ok'],
            'error'   => $resultado['error'],
            'total_vulnerabilidades_unicas' => count($resultado['vulnerabilidades']),
            'vulnerabilidades' => $resultado['vulnerabilidades'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;

    case 'get_prompt_default':
        session_write_close();

        $secciones = [
            'RESUMEN'      => $min_palabras,
            'HALLAZGOS'    => $max_palabras,
            'CONCLUSIONES' => $medium_palabras,
        ];

        $secciones_texto = '';
        foreach ($secciones as $nombre => $cant) {
            $secciones_texto .= "• $nombre (aprox. $cant palabras)\n";
        }

        $explicacion = "El prompt predefinido le pide a la IA que actúe como analista de ciberseguridad y genere "
            . "un resumen del documento con las siguientes secciones:\n\n"
            . $secciones_texto . "\n"
            . "Para la sección HALLAZGOS, además, le indica que:\n"
            . "• Priorice vulnerabilidades con exploits públicos conocidos o explotación activa confirmada.\n"
            . "• Use como fuente prioritaria el catálogo oficial CISA KEV cuando esté disponible (se agrega "
            . "automáticamente al análisis, en ambos modos de prompt).\n"
            . "• Incluya el CVE asociado a cada hallazgo, si está disponible.\n"
            . "• Mencione si hay parche disponible o no, y la antigüedad del CVE cuando el documento lo indique.\n"
            . "• Ordene los hallazgos de mayor a menor severidad, usando únicamente las categorías que el "
            . "propio documento utilice (sin inventar niveles que el documento no mencione).\n\n"
            . "La sección TRADUCCIÓN es condicional: solo aparece si la IA detecta palabras de la traducción "
            . "automática que suenan a español neutro/de España en vez de "
            . "jerga técnica rioplatense habitual. Si no encuentra ninguna, la omite por completo.\n";

        echo json_encode(['explicacion' => $explicacion]);
        exit;

    case 'eliminar_resumen_documento_ia':
        session_write_close();
        $id_fila = (int) ($_POST['id_fila'] ?? 0);

        if (!$id_fila) {
            echo json_encode(['error' => 'Falta id_fila']);
            exit;
        }

        $ok = $integracion->eliminar_resumen_documento_ia_por_fila($id_fila);

        echo json_encode([
            'status'  => $ok ? 'success' : 'error',
            'mensaje' => $ok ? 'Resumen eliminado correctamente' : 'No se pudo eliminar el resumen',
        ]);
        exit;

    default:
        # code...
        break;
}
