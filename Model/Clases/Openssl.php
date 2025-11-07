<?php
require_once __DIR__ . "/../../Config/Config.php";

class Openssl
{
    private static $AES = "AES-256-ECB";

    public static function set_ssl_encrypt(string $val)
    {
        $encrypted = openssl_encrypt($val, self::$AES, KEY);
        if ($encrypted == false) {
            return $val;
        } else {
            return urlencode(base64_encode($encrypted));
        }
    }

    public static function get_ssl_decrypt(string $val)
    {
        $decoded = base64_decode(urldecode($val));
        $decrypted = openssl_decrypt($decoded, self::$AES, KEY);

        if ($decrypted == false || $decrypted == null) {
            return $val;
        } else {
            return $decrypted;
        }
    }
}
