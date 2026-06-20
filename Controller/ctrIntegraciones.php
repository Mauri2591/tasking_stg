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

function llamarClaudeDirecto(string $prompt, int $timeout = 480): array
{
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            'model'      => 'claude-sonnet-4-6',
            'max_tokens' => 7552,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-api-key: ' . ANTHROPIC_API_KEY,
            'anthropic-version: 2023-06-01',
        ],
        CURLOPT_TIMEOUT => $timeout,
    ]);

    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        return ['ok' => false, 'texto' => '', 'http_code' => 0, 'error' => "Error de conexión: $err"];
    }

    $data = json_decode($raw, true);

    if ($code !== 200) {
        $mensaje_error = $data['error']['message'] ?? 'Error desconocido de la API de Claude';
        return ['ok' => false, 'texto' => '', 'http_code' => $code, 'error' => $mensaje_error];
    }

    $texto = $data['content'][0]['text'] ?? '';

    return ['ok' => true, 'texto' => $texto, 'http_code' => $code, 'error' => null];
}


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
            $integracion->liberar_lock_generacion_ia($id);
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
        ignore_user_abort(true); // controlo el corte, en vez de que PHP lo mate de golpe sin liberar el lock

        $JWT_PRUEBA = JWT_API_GENIA;

        $id     = (int) ($_POST['id'] ?? 0);
        $modelo = trim($_POST['modelo'] ?? 'Gemini 2.5 Flash');
        $modo   = trim($_POST['modo'] ?? 'default'); // 'default' | 'personalizado'
        $proveedor = trim($_POST['proveedor'] ?? 'genia'); // 'genia' | 'claude'
        $prompt_personalizado = trim($_POST['prompt_personalizado'] ?? '');

        if (!$id) {
            echo json_encode(['error' => 'Falta id']);
            exit;
        }

        if ($modo === 'personalizado' && $prompt_personalizado === '') {
            echo json_encode(['error' => 'Falta el prompt personalizado']);
            exit;
        }

        // --- Lock para evitar generaciones duplicadas en paralelo ---
        $minutos_lock_stale = 10;

        $lock = $integracion->obtener_lock_generacion_ia($id);
        if ($lock) {
            $minutos_transcurridos = (strtotime('now') - strtotime($lock['fech_inicio'])) / 60;
            if ($minutos_transcurridos < $minutos_lock_stale) {
                http_response_code(409);
                echo json_encode([
                    'error'   => 'ya_en_curso',
                    'mensaje' => 'Ya hay una generación de resumen en curso para este proyecto. Esperá unos minutos e intentá nuevamente.',
                ]);
                exit;
            }
        }

        $integracion->crear_lock_generacion_ia($id, $_SESSION['usu_id']);

        $secciones = [
            'RESUMEN'                    => $min_palabras,
            'HALLAZGOS'                  => $max_palabras,
            'CONCLUSIONES'               => $medium_palabras,
            'TRADUCCIÓN'                 => $medium_palabras,
        ];

        $info = $proyecto->get_archivos_por_id_descripciones_proyecto($id);

        if (!$info || empty($info['carpeta_documentos_proy']) || empty($info['documento'])) {
            $integracion->liberar_lock_generacion_ia($id); // ← agregar esta línea, ya que acá el lock SÍ se creó
            echo json_encode(['error' => 'No se encontraron archivos para ese id']);
            exit;
        }

        $tipo_servicio = $info['categoria'] ?? 'Ciberseguridad';

        $archivos = array_values(array_filter(array_map('trim', explode(',', $info['documento']))));
        $carpeta  = $info['carpeta_documentos_proy'];

        $detalle_secciones = '';
        foreach ($secciones as $nombre => $cant_palabras) {
            $detalle_secciones .= "- $nombre: aproximadamente $cant_palabras palabras\n";
        }

       $guardrails_antialucinacion = "\n\nReglas que tenés que respetar SIEMPRE, sin excepción, sin importar qué te pidan generar:\n"
            . "- PROHIBICIÓN ESTRICTA sobre CVEs: NUNCA inventes, sugieras o \"adivines\" un CVE que no esté "
            . "literalmente escrito en el documento o en el bloque CONTEXTO VERIFICADO. Si no hay CVE disponible "
            . "para un hallazgo, decilo así o simplemente omitilo — NUNCA propongas un CVE \"posible\" o \"probable\" "
            . "basado en tu conocimiento general, aunque exista en la realidad.\n"
            . "- No afirmes que existe o no existe parche disponible si el documento no lo menciona explícitamente. "
            . "En ese caso omitilo, no lo completes con tu conocimiento general.\n"
            . "- No uses la palabra \"crítica\"/\"crítico\"/\"críticas\"/\"críticos\" para referirte a la severidad de "
            . "un hallazgo, salvo que esa palabra exacta aparezca como clasificación de severidad de ese hallazgo "
            . "específico en el documento original. Usá \"grave\", \"de alto impacto\" o \"de alta severidad\" si "
            . "necesitás enfatizar gravedad.\n"
            . "- Si más abajo se incluye un bloque \"CONTEXTO VERIFICADO\" con datos del catálogo CISA KEV, usalo "
            . "como fuente PRIORITARIA y confiable por sobre tu conocimiento general — es información oficial y "
            . "actualizada, y las coincidencias \"aproximadas\" deben mencionarse usando exactamente la frase "
            . "\"(según catálogo CISA KEV, posible relación con esta familia de producto — no confirmado para esta "
            . "versión exacta)\" en vez de abreviar con \"aprox.\" o similar, para que quede claro que no es una "
            . "alucinación sino un match de producto.\n"
            . "- Si determinás que una posible coincidencia con CISA KEV NO corresponde al hallazgo (vendor/producto "
            . "no relacionado), NO la menciones en absoluto, ni siquiera para descartarla o aclarar que no aplica. "
            . "Simplemente omitila por completo, como si no existiera. Mencionar un CVE o vendor irrelevante y luego "
            . "aclarar que \"no corresponde\" sigue siendo ruido confuso — la regla es: si no aplica, no se menciona, "
            . "punto.\n";

        // Instrucción base: la fija del sistema (modo default) o la que escribió el usuario (modo personalizado)

        if ($modo === 'personalizado') {

            $instruccion_base = "Actuá como analista de ciberseguridad. "
                . "A continuación tenés el contenido completo de un documento.\n\n"
                . "Seguí las instrucciones específicas del usuario sobre qué generar:\n"
                . $prompt_personalizado . "\n"
                . $guardrails_antialucinacion;

        } else {

            $instruccion_base = "Actuá como analista de ciberseguridad que realizó el siguiente servicio: $tipo_servicio. "
                . "A continuación tenés el contenido completo de un documento de un proyecto de $tipo_servicio.\n\n"
                . "El documento contiene un informe técnico. Generá un resumen enfocado ÚNICAMENTE en estas secciones, "
                . "respetando la cantidad de palabras indicada para cada una:\n"
                . $detalle_secciones . "\n"
                . "No incluyas ninguna otra sección además de las listadas arriba, aunque el documento original tenga "
                . "secciones como Objetivos, Alcance, Recomendaciones Generales, Actividades Realizadas o Metodología "
                . "— esas secciones deben omitirse completamente del resultado, sin excepción, sin mencionarlas ni "
                . "resumirlas.\n\n";

            $instruccion_base .= "Para la sección HALLAZGOS, mantené TODO el contenido sustancial (CVE, relación con "
                . "CISA KEV, antigüedad del CVE, disponibilidad de parche, qué representa mayor riesgo real de "
                . "explotación), pero eliminá el relleno de prosa. Formato por nivel de severidad:\n"
                . "[Cantidad] [Severidad]: [nombre 1] (CVE-XXXX-XXXX, en KEV/explotada activamente si corresponde), "
                . "[nombre 2] (CVE-XXXX-XXXX); resto sin detalle CVE/KEV relevante.\n\n"
                . "PROHIBIDO usar estas frases de relleno (no es una lista exhaustiva, es el patrón a evitar): "
                . "\"Se detectaron\", \"Se identificaron\", \"se hallaron\", \"ambas permiten\", \"con parche "
                . "disponible según el documento\" (si ya mencionaste el CVE, no hace falta repetir \"según el "
                . "documento\" en cada ítem), cualquier oración completa con sujeto+verbo+complemento que solo "
                . "introduce el dato sin agregar información nueva.\n"
                . "SÍ mantené: el CVE específico, si está confirmado en KEV o es match aproximado, antigüedad del "
                . "CVE si es relevante para priorizar, disponibilidad de parche si el documento la menciona, cuál "
                . "representa mayor riesgo real de explotación.\n"
                . "Ejemplo de la diferencia:\n"
                . "MAL (relleno): \"Se detectaron 2 vulnerabilidades de severidad Crítica: Apache HTTP Server "
                . "(CVE-2022-31813, CVSS 9.8, con parche disponible según el documento) y PHP múltiples "
                . "vulnerabilidades (CVE-2020-7060 y CVE-2020-7059, CVSS 9.1, con parche disponible según el "
                . "documento); ambas permiten compromiso total del sistema.\"\n"
                . "BIEN (mismo contenido, sin relleno): \"2 Críticas: Apache mod_proxy (CVE-2022-31813, compromiso "
                . "total, parcheable), PHP RCE (CVE-2020-7060/7059, parcheable). Ninguna confirmada en KEV.\"\n\n";

            $instruccion_base .= "OBLIGATORIO: cubrí TODOS los niveles de severidad presentes en el documento, "
                . "incluyendo Informativa o Baja — no omitas ninguno aunque parezca menor. Usá ÚNICAMENTE las "
                . "categorías de severidad que figuren en el propio documento (ej: si el documento solo clasifica "
                . "como Alta/Media/Baja/Informativa, NO inventes ni menciones la categoría \"Crítica\" salvo que el "
                . "documento la use explícitamente). Ordená de mayor a menor severidad.\n\n"
                . "ADVERTENCIA sobre conteo: al indicar la \"Cantidad\" de cada nivel de severidad, contá NÚMERO DE "
                . "VULNERABILIDADES DISTINTAS (tipos de hallazgo), NUNCA la cantidad de hosts/puertos/ocurrencias. "
                . "Si el documento dice \"Ocurrencias: 4\" para un hallazgo, eso significa que ESE MISMO hallazgo "
                . "afecta 4 instancias — sigue siendo 1 sola vulnerabilidad, no 4. Verificá que la suma de tus "
                . "conteos por severidad coincida con la cantidad total de hallazgos distintos del documento.\n\n";

            $instruccion_base .= "PROHIBICIÓN ADICIONAL sobre CISA KEV: NUNCA inventes una relación con un vendor o "
                . "producto del catálogo CISA KEV que no esté literalmente presente en el bloque CONTEXTO VERIFICADO "
                . "provisto más abajo en este mensaje. Si no hay bloque CONTEXTO VERIFICADO, o ese bloque no menciona "
                . "explícitamente el hallazgo en cuestión, NO mencionés ningún vendor, CVE, ni \"posible relación con "
                . "CISA KEV\" para ese hallazgo — ni siquiera de forma indirecta o especulativa. Inventar una "
                . "asociación con un vendor no relacionado (ej: atribuir CISA KEV de \"Check Point\" a un hallazgo de "
                . "PHP) es tan grave como inventar un CVE directo.\n\n";

            $instruccion_base .= "Para la sección TRADUCCIÓN (a diferencia de RESUMEN, HALLAZGOS y CONCLUSIONES, "
                . "esta sección es CONDICIONAL): los documentos analizados suelen provenir de un escáner en inglés "
                . "que pasa por una herramienta de traducción automática antes de llegar a vos. Esta sección sirve "
                . "para detectar dos tipos de problemas en el texto en español del documento:\n"
                . "1) Palabras ya traducidas al español que usan vocabulario neutro o de España en vez del español "
                . "técnico habitual en Argentina (por ejemplo: el término que en España se usa para un objeto "
                . "físico que abre cerraduras, cuando en Argentina ese mismo concepto en contexto de autenticación "
                . "o criptografía se nombra distinto).\n"
                . "2) Frases sin sentido o gramaticalmente rotas, que no son palabras reales en ningún idioma y "
                . "rompen la gramática de la oración donde aparecen — síntoma típico de un error de OCR o de la "
                . "herramienta de traducción automática.\n"
                . "IMPORTANTE: para errores del tipo (2), texto corrupto/sin sentido, NO propongas una traducción "
                . "ni una corrección en otro idioma del que tenía originalmente la palabra rota — limitate a "
                . "señalar \"texto posiblemente corrupto, revisar documento original\" sin sugerir una palabra de "
                . "reemplazo si no podés determinar con certeza cuál era la palabra correcta y en qué idioma "
                . "estaba.\n"
                . "Estos son los ÚNICOS DOS tipos de problema a buscar — no inventes una tercera categoría como "
                . "\"inconsistencia de estilo\", falta de tildes, redundancia, o mezcla de idioma en nombres propios "
                . "de productos/protocolos (ej: \"HTTP Server\" es el nombre técnico estándar de un producto, no una "
                . "traducción a corregir). Si el problema no encaja EXACTAMENTE en (1) o (2), no lo menciones.\n"
                . "IMPORTANTE - NO es el objetivo de esta sección: palabras o términos que quedaron SIN TRADUCIR, es "
                . "decir que todavía están en inglés en el documento (ejemplos: \"ciphers\", \"backdoor\", \"patch\", "
                . "nombres de productos o protocolos). Esos términos en inglés NO deben aparecer en esta sección bajo "
                . "ninguna circunstancia, ya estén o no traducidos al español en otra parte del documento — son "
                . "terminología técnica válida y se dejan como están.\n"
                . "REQUISITO OBLIGATORIO DE EVIDENCIA — esta es la regla más importante de toda la sección: para "
                . "CADA entrada que reportes, primero tenés que citar textualmente, entre comillas, la oración "
                . "COMPLETA del documento original donde aparece la palabra o frase problemática. Si al intentar "
                . "hacer esa cita no encontrás ninguna oración real del documento que contenga ese problema, "
                . "significa que lo estás imaginando — en ese caso NO la reportes, sin excepción. Nunca reportes "
                . "una palabra o frase basándote en que \"suele\" o \"podría\" aparecer en este tipo de documentos "
                . "— solo en lo que efectivamente leíste en ESTE documento puntual.\n"
                . "Formato exacto: \"[cita textual de la oración completa del documento]\" — palabra/frase "
                . "problemática: \"X\" → sugerencia: \"Y\" (breve motivo).\n"
                . "Si NO encontrás ningún problema de los dos tipos descriptos arriba, NO incluyas la sección "
                . "TRADUCCIÓN en absoluto bajo NINGUNA circunstancia — ni el encabezado, ni un mensaje de \"no se "
                . "encontraron problemas\", ni ninguna nota o comentario meta explicando esta regla. Si no hay nada "
                . "que reportar, tu respuesta debe terminar en la sección CONCLUSIONES, sin ningún texto adicional "
                . "después. Esta instrucción es literal: el carácter de fin de tu respuesta debe ser el último "
                . "carácter de CONCLUSIONES.\n\n";

            $instruccion_base .= "Formato de salida: usá el nombre de cada sección en mayúsculas como encabezado, "
                . "seguido del texto correspondiente, sin numeración ni viñetas adicionales.\n\n"
                . $guardrails_antialucinacion;
        }
        $kev = new CisaKevChecker(BASE_PATH . "Cache/cisa_kev.json");

        $resultados = [];

        foreach ($archivos as $archivo) {
            if (connection_aborted()) {
                $integracion->liberar_lock_generacion_ia($id);
                exit;
            }
            $ruta = BASE_PATH . "View/Home/Public/Uploads/Proyectos/" . $carpeta . "/" . $archivo;
            $ext  = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

            $extraido = ExtractorDocumentos::extraer($ruta);

            if (!$extraido['ok']) {
                $resultados[] = [
                    'documento' => $archivo,
                    'resumen'   => null,
                    'error'     => $extraido['error'],
                ];
                continue;
            }

            // En modo default el cruce con CISA siempre aplica, sin importar el área/sector —
            // el prompt default ya está armado para análisis de vulnerabilidades en cualquier servicio
            // de ciberseguridad (EH, SOC, IR, Calidad y Procesos, etc.), no es exclusivo de EH.
            // En modo personalizado, solo aplica si hay evidencia real de que el contenido trata
            // sobre vulnerabilidades/CVEs/riesgo (prompt del usuario o texto del documento).
            $aplica_cisa = ($modo === 'default')
                || CisaKevChecker::pareceContenidoDeVulnerabilidades($prompt_personalizado)
                || CisaKevChecker::pareceContenidoDeVulnerabilidades($extraido['texto']);

            $contexto_verificado = '';

            if ($aplica_cisa && in_array($ext, ['xlsx', 'xls'], true)) {
                $vulns = ExtractorDocumentos::extraerVulnerabilidadesXlsx($ruta);
                if ($vulns['ok'] && !empty($vulns['vulnerabilidades'])) {
                    $contexto_verificado = $kev->generarContextoVerificado($vulns['vulnerabilidades']);
                }
            }

            // Para documentos narrativos (docx/pdf), buscamos CVEs explícitos mencionados en el texto
            if ($aplica_cisa && $contexto_verificado === '' && !in_array($ext, ['xlsx', 'xls'], true)) {
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

            $texto_para_prompt = $extraido['texto'];
            $limite_caracteres = 60000;

            if (mb_strlen($texto_para_prompt) > $limite_caracteres) {
                $texto_para_prompt = mb_substr($texto_para_prompt, 0, $limite_caracteres)
                    . "\n\n[NOTA: El documento original es más extenso y fue truncado para el análisis. "
                    . "Es posible que falten hallazgos del final del documento.]";
            }

            $prompt = $instruccion_base
                . $contexto_verificado
                . "=== Contenido del documento ===\n"
                . $texto_para_prompt;

            if ($proveedor === 'claude') {
                $resultado_ia = llamarClaudeDirecto($prompt);

                if (in_array($resultado_ia['http_code'], [401, 403], true)) {
                    $integracion->liberar_lock_generacion_ia($id);
                    http_response_code(401);
                    echo json_encode([
                        'error'   => 'api_key_invalida',
                        'mensaje' => 'La API key de Claude no es válida o no tiene permisos. Revisá ANTHROPIC_API_KEY en el .env.',
                    ]);
                    exit;
                }

                if ($resultado_ia['http_code'] === 429) {
                    $integracion->liberar_lock_generacion_ia($id);
                    http_response_code(429);
                    echo json_encode([
                        'error'   => 'rate_limit',
                        'mensaje' => 'Se alcanzó el límite de solicitudes a la API de Claude. Esperá un par de minutos e intentá nuevamente.',
                    ]);
                    exit;
                }

                if (!$resultado_ia['ok']) {
                    $resultados[] = [
                        'documento' => $archivo,
                        'resumen'   => null,
                        'error'     => 'Error de Claude: ' . $resultado_ia['error'],
                    ];
                    continue;
                }

                $texto_final = $resultado_ia['texto'];
                $modelo_para_guardar = 'Claude Sonnet 4.6';
            } else {
                // --- Genia (comportamiento original) ---
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
                    CURLOPT_TIMEOUT => 480,
                ]);

                $raw = curl_exec($ch);
                $http_code_doc = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code_doc !== 200) {
                    error_log("GenAI error - documento: $archivo | http_code: $http_code_doc | respuesta: " . mb_substr($raw, 0, 1000));
                }

                if (in_array($http_code_doc, [401, 403], true)) {
                    $integracion->liberar_lock_generacion_ia($id);
                    http_response_code(401);
                    echo json_encode([
                        'error'   => 'jwt_caducado',
                        'mensaje' => 'El token de acceso a la plataforma GenAI venció o no es válido. Avisale a Mauricio para que lo renueve.',
                    ]);
                    exit;
                }

                if ($http_code_doc === 429) {
                    $integracion->liberar_lock_generacion_ia($id);
                    http_response_code(429);
                    echo json_encode([
                        'error'   => 'rate_limit',
                        'mensaje' => 'Se alcanzó el límite de solicitudes a la plataforma GenAI. Esperá unos minutos, o reintentá usando Claude como proveedor alternativo.',
                    ]);
                    exit;
                }

                if ($raw === false) {
                    $resultados[] = [
                        'documento' => $archivo,
                        'resumen'   => null,
                        'error'     => 'Tiempo de espera agotado al generar el resumen de este documento (puede ser muy extenso). Intentá generarlo de nuevo.',
                    ];
                    continue;
                }

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

                $modelo_para_guardar = $modelo;
            }

            if ($texto_final === '') {
                $resultados[] = [
                    'documento' => $archivo,
                    'resumen'   => null,
                    'error'     => 'La IA no devolvió contenido para este documento. Intentá generarlo de nuevo.',
                ];
                continue;
            }

            $resultados[] = [
                'documento' => $archivo,
                'resumen'   => $texto_final,
                'error'     => null,
            ];

            $integracion->insertar_resumen_documento_ia(
                $id,
                $archivo,
                $texto_final,
                $modelo_para_guardar,
                $_SESSION['usu_id'],
                $modo,
                $modo === 'personalizado' ? $prompt_personalizado : null
            );
        }

        $integracion->liberar_lock_generacion_ia($id);
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
