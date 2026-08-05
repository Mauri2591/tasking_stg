<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/GestionActivos.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Model/Clases/Validaciones.php";
require_once __DIR__ . "/../Model/Clases/Headers.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";

$gestion_activos = new GestionActivos();
switch ($_GET['case']) {
    case 'get_activos':
        header('Content-Type: application/json');
        echo json_encode($gestion_activos->get_activos($_SESSION['sector_id']));
        break;

    default:
        echo json_encode("Endpoint no encontrado");
        break;
}
