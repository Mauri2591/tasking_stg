<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

session_name($_ENV['COOKIE_SESSION']);
ini_set('session.cookie_httponly', 1);      // Bloquea acceso desde JS
// ini_set('session.cookie_secure', 1);        // Solo HTTPS
ini_set('session.cookie_samesite', 'Strict'); // Protege contra CSRF
session_start();

class Conexion
{
    private $conexion;

    public function __construct()
    {
        try {
            $host   = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $user   = $_ENV['DB_USER'];
            $pass   = $_ENV['DB_PASS'];

            $this->conexion = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }

    public function get_conexion()
    {
        return $this->conexion;
    }
}