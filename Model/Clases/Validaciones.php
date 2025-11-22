    <?php
    class Validaciones
    {
        public function parse_hosts($raw, $tipo)
        {
            $lines = preg_split('/\r\n|\r|\n/', trim($raw));
            $cleaned = array_filter(array_map('trim', $lines));

            if ($tipo === 'IP') {
                $cleaned = array_filter($cleaned, fn($ip) => filter_var($ip, FILTER_VALIDATE_IP));
            } elseif ($tipo === 'URL') {
                $cleaned = array_filter(
                    $cleaned,
                    fn($url) =>
                    preg_match('/^https?:\/\/[^\s]+$/', $url)
                );
            }

            return array_map(fn($val) => ['valor' => htmlspecialchars($val, ENT_QUOTES), 'tipo' => $tipo], $cleaned);
        }

        public static function subida_archivo($data)
        {
            if (!isset($data['name']) || $data['error'] !== 0) {
                return null;
            }

            $extensiones_permitidas = ["jpeg", "jpg", "png", "txt", "doc", "docx", "pdf", "zip"];
            $tipos_mime_permitidos = [
                'image/jpeg',
                'image/png',
                'text/plain',
                'application/x-empty',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip'
            ];

            $extension = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
            $mime_type = mime_content_type($data['tmp_name']);

            if (!in_array($extension, $extensiones_permitidas) || !in_array($mime_type, $tipos_mime_permitidos)) {
                http_response_code(400);
                echo json_encode(["Status" => "Error", "Message" => "Archivo no permitido"]);
                exit;
            }

            $ruta_archivos = __DIR__ . "../../../View/Home/Public/Uploads/Calidad/";
            if (!is_dir($ruta_archivos)) {
                mkdir($ruta_archivos, 0777, true);
            }

            $nombre_archivo = uniqid(md5(rand()), true) . ".$extension";
            $ruta_final = $ruta_archivos . $nombre_archivo;

            if (move_uploaded_file($data['tmp_name'], $ruta_final)) {
                return $nombre_archivo;
            } else {
                return null;
            }
        }

        // Fuera de la función
        public static array $errores_archivos = [];

      
public static function guardar_archivo_descripcion_proyecto($data, $hash_folder)
{
    // Validación inicial
    if (!isset($data['name']) || $data['error'] !== 0) {
        self::$errores_archivos[] = "{$data['name']} - Error en subida inicial.";
        return null;
    }

    // Extensiones y MIME permitidos
    $extensiones_permitidas = ["jpeg", "jpg", "png", "txt", "xls", "xlsx", "doc", "docx", "pdf", "zip"];
    $tipos_mime_permitidos = [
        'image/jpeg',
        'image/png',
        'text/plain',
        'application/x-empty',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/octet-stream'
    ];

    $extension = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $mime_type = mime_content_type($data['tmp_name']);

    // Excepción para DOCX
    if ($extension === 'docx' && in_array($mime_type, ['application/zip', 'application/octet-stream'])) {
        // válido
    } elseif (!in_array($extension, $extensiones_permitidas) || !in_array($mime_type, $tipos_mime_permitidos)) {
        self::$errores_archivos[] = "{$data['name']} - MIME no permitido: $mime_type";
        return null;
    }

    // Limpieza del hash para evitar caracteres raros
    $hash_folder = preg_replace('/[^A-Za-z0-9_\-]/', '', $hash_folder);

    // Construcción segura de la ruta
    $ruta_archivos = dirname(__DIR__, 3) . "/View/Home/Public/Uploads/Proyectos/" . $hash_folder . "/";
    error_log("Ruta final para guardar archivo: $ruta_archivos"); // Log para depuración

    // Crear carpeta si no existe
    if (!is_dir($ruta_archivos)) {
        if (!mkdir($ruta_archivos, 0777, true)) {
            self::$errores_archivos[] = "No se pudo crear el directorio $ruta_archivos";
            return null;
        }
    }

    // Nombre seguro para el archivo
    $nombre_archivo = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($data['name']));
    $ruta_final = $ruta_archivos . $nombre_archivo;

    // Intentar mover el archivo
    if (move_uploaded_file($data['tmp_name'], $ruta_final)) {
        return $nombre_archivo;
    } else {
        self::$errores_archivos[] = "{$data['name']} - Falló al mover el archivo a $ruta_final.";
        return null;
    }
}

    }
