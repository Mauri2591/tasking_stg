<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Conexion
{
    private $conexion;

    public function __construct()
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];
            $this->conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Error en la conexión: " . $e->getMessage();
        }
    }

    protected function get_conexion()
    {
        return $this->conexion;
    }
}
