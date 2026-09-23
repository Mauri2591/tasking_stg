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

class ConexionTaskingViejo
{
    private $conexion;
    public function __construct()
    {
        try {
            // $conectar = $this->conexion = new PDO("mysql:local=localhost;dbname=tasking", "tasking", "TaskUser*2024");
            $conectar = $this->conexion = new PDO("mysql:host=localhost;dbname=tasking", "root", "");
            return $conectar;
        } catch (Exception $e) {
            print "Error BD Tasking Viejo!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function get_conexion_tasking_viejo()
    {
        return $this->conexion;
    }
}


class ConexionVulmaGestion
{
    private $conexion;
    public function __construct()
    {
        try {
            // $conectar = $this->conexion = new PDO("mysql:local=localhost;dbname=vulma_gestion", "vulma_gestion", "wRAj%7D9KT9#SV");                
            $conectar = $this->conexion = new PDO("mysql:host=localhost;dbname=vulma_gestion", "root", "");         
            return $conectar;
        } catch (Exception $e) {
            print "Error BD Vulma Gestion!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function get_conexion_vulma_gestion()
    {
        return $this->conexion;
    }
}
