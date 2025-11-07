<?php

class Headers
{
    public static function get_cors()
    {
        header('Access-Control-Allow-Origin: *');
    }

    public static function get_csp()
    {
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'self'; style-src 'self' 'unsafe-inline';");
    }

    public static function get_mime($data)
    {
        $mime = mime_content_type($data['tmp_name']);
        $tipos_archivos_permitidas = array('image/jpg', 'image/png', 'image/jpeg');
        if (in_array($mime, $tipos_archivos_permitidas)) {
            header("Content-type: $mime");
            http_response_code(200);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "messaje" => "mime no permitido"]);
        }
    }
}
