<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Proyectos.php";
require_once __DIR__ . "/../Model/Clases/Reportes.php";
require_once __DIR__ . "/../Model/Timesummary.php";

// Instanciamos las clases una sola vez
$reporte = new Reportes();
$proyecto = new Proyectos();
$timesummary = new Timesummary();

switch ($_GET['case'] ?? null) {

    case 'excel':
        $client_id = $_GET['client_id'] ?? null;

        if (!$client_id) {
            die("Falta el ID del cliente.");
        }

        // Traemos los datos del cliente específico (sector 4 = todos los proyectos)
        if ($_SESSION['sector_id'] == "4") {
            $data = $proyecto->get_proyectos_total_x_client_id($client_id, 4);
        } else {
            $data = $proyecto->get_proyectos_total_x_client_id($client_id, $_SESSION['sector_id']);
        }

        if (empty($data)) {
            die("No hay datos para generar el reporte.");
        }

        $nombre_cliente = $data[0]['cliente'] ?? 'CLIENTE';

        // Generamos el Excel individual
        $reporte::get_reporte_excel($data, "PROYECTOS_{$nombre_cliente}");
        break;


    case 'total_excel':
        $fecha_desde = $_POST['fecha_desde'] ?? null;
        $fecha_hasta = $_POST['fecha_hasta'] ?? null;
        $data = $proyecto->get_proyectos_total_excel($fecha_desde, $fecha_hasta);

        if (empty($data)) {
            http_response_code(404);
            header("Location:" . URL . "/View/Home/Gestion/Clientes/Proyectos/?doc=error");
            exit;
        }
        $reporte::total_excel($data, "PROYECTOS_TOTAL");
        break;

    case 'getDatosReporteSinFiltro':

        $fechaDesde = $_POST['hora_desde_edit'] ?? null;
        $fechaHasta = $_POST['hora_hasta_edit'] ?? null;
        $idClienteDocx = $_POST['hiddenIdClienteDocx'] ?? null;
        $idClienteXlsx = $_POST['hiddenIdClienteXlsx'] ?? null;

        // Normalizar cliente (puede venir de Docx o Xlsx)
        $idCliente = $idClienteDocx ?: $idClienteXlsx;

        // 1️⃣ FECHAS + CLIENTE (más específica)
        if ((!empty($fechaDesde) || !empty($fechaHasta)) && !empty($idCliente)) {
            $data = $timesummary->getReportePorFechasYCliente($idCliente, $fechaDesde, $fechaHasta);
            $nombreReporte = "Timesummary";
        }
        // 2️⃣ SOLO FECHAS
        else if (!empty($fechaDesde) || !empty($fechaHasta)) {
            $data = $timesummary->getDatosReporteConFiltroFechas($fechaDesde, $fechaHasta);
            $nombreReporte = "Timesummary";
        }
        // 3️⃣ SOLO CLIENTE
        else if (!empty($idCliente)) {
            $data = $timesummary->getDatosReporteConFiltroPoriDCliente($idCliente);
            $nombreReporte = "Timesummary";
        }
        // 4️⃣ SIN FILTROS
        else {
            $data = $timesummary->getDatosReporteSinFiltro();
            $nombreReporte = "Timesummary";
        }

        if (isset($_POST['generarReporteDocx'])) {
            Reportes::getDatosReporteSinFiltroDocx($data, $nombreReporte);
            exit;
        }

        if (isset($_POST['generarReporteXlsx'])) {
            Reportes::getDatosReporteSinFiltroXlsx($data, $nombreReporte);
            exit;
        }
        break;

    default:
        echo "Acción no reconocida";
        break;
}
