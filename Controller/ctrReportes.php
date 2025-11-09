<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Model/Proyectos.php";
require_once __DIR__ . "/../Model/Clases/Reportes.php";

// Instanciamos las clases una sola vez
$reporte = new Reportes();
$proyecto = new Proyectos();

switch ($_GET['case'] ?? null) {
    case 'excel':
        $client_id = $_GET['client_id'] ?? null;

        if (!$client_id) {
            die("Falta el ID del cliente.");
        }

        // Traemos los datos del cliente específico
        $data = $proyecto->get_proyectos_total_x_client_id($client_id);
        $nombre_cliente = $data[0]['cliente'];

        // Generamos el Excel individual
        $reporte::get_reporte_excel($data, "PROYECTOS_{$nombre_cliente}");
        break;

    case 'total_excel':
        $data = $proyecto->get_proyectos_total_excel();
        $reporte::total_excel($data, "PROYECTOS_TOTAL");
        break;

    default:
        echo "Acción no reconocida";
        break;
}
