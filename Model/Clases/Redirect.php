<?php
require_once __DIR__ . '/Openssl.php';

class Redirect
{
    public static function validateProyectoParams(): array
    {
        // Función auxiliar interna
        $redirect_home = function () {
            header('Location: ' . URL . "/View/Home/");
            exit;
        };

        // 1) chequear que vengan ambos parámetros
        $p_raw  = filter_input(INPUT_GET, 'p', FILTER_DEFAULT);
        $pg_raw = filter_input(INPUT_GET, 'pg', FILTER_DEFAULT);
        if (empty($p_raw) || empty($pg_raw)) $redirect_home();

        // 2) desencriptar UNA sola vez
        $p_dec  = Openssl::get_ssl_decrypt($p_raw);
        $pg_dec = Openssl::get_ssl_decrypt($pg_raw);

        // 3) si falla la desencriptación -> manipulación
        if ($p_dec === false || $p_dec === null || $pg_dec === false || $pg_dec === null) {
            $redirect_home();
        }

        // 4) validar formato (asumo que tus ids son enteros)
        if (!ctype_digit((string)$p_dec) || !ctype_digit((string)$pg_dec)) {
            $redirect_home();
        }

        // Cast a int y devolver como arreglo
        return [
            'p_id'  => (int)$p_dec,  // id_proyecto_cantidad_servicios
            'pg_id' => (int)$pg_dec  // id_proyecto_gestionado
        ];
    }
}
