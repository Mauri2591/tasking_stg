<?php
require_once __DIR__."/../../Config/Conexion.php";
require_once __DIR__."/../Model/Proyectos.php";
$proy= new ProyectosVulmaGestion();
switch ($_GET['case']) {
    case 'proyectos_eh':
        $datos=$proy->proyectos_eh();
        echo json_encode($datos);
        break;
    
    default:
        echo json_encode(["Error" => "Case no valido en Vulma Gestion"]);
        break;
}