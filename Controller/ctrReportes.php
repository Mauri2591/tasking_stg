<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Proyectos.php";
require_once __DIR__ . "/../Model/Clases/Reportes.php";
require_once __DIR__ . "/../Model/Timesummary.php";
require_once __DIR__ . "/../Model/Auditoria.php";

// Instancio las clases una sola vez
$reporte = new Reportes();
$proyecto = new Proyectos();
$timesummary = new Timesummary();
$audit = new Auditoria();

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

    case 'reporteExcelProyectosCrossSell':
        $data = $proyecto->getClientesConSectorSinContratar();
        Reportes::reporteExcelProyectosCrossSell($data);
        break;

    case 'getDatosReporteSinFiltro':

        $fechaDesde = $_POST['hora_desde_edit'] ?? null;
        $fechaHasta = $_POST['hora_hasta_edit'] ?? null;
        $idClienteDocx = $_POST['hiddenIdClienteDocx'] ?? null;
        $idClienteXlsx = $_POST['hiddenIdClienteXlsx'] ?? null;

        $idCliente = $idClienteDocx ?: $idClienteXlsx;

        // Se define UNA SOLA VEZ antes de todos los if
        $sector_id = null;
        if ($_SESSION['lider'] == "SI" && $_SESSION['sector_id'] != "4") {
            $sector_id = $_SESSION['sector_id'];
        }

        if ((!empty($fechaDesde) || !empty($fechaHasta)) && !empty($idCliente)) {
            $data = $timesummary->getReportePorFechasYCliente($idCliente, $fechaDesde, $fechaHasta, $sector_id);
            $nombreReporte = "Timesummary";
        } else if (!empty($fechaDesde) || !empty($fechaHasta)) {
            $data = $timesummary->getDatosReporteConFiltroFechas($fechaDesde, $fechaHasta, $sector_id);
            $nombreReporte = "Timesummary";
        } else if (!empty($idCliente)) {
            $data = $timesummary->getDatosReporteConFiltroPoriDCliente($idCliente, $sector_id);
            $nombreReporte = "Timesummary";
        } else {
            $data = $timesummary->getDatosReporteSinFiltro($sector_id);
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

    case 'get_audit_sesiones_x_fecha':
        $desde = $_GET['desde'] ?? '';
        $hasta_formateado = $_GET['hasta'] ?? '';

        if (!empty($desde) && !empty($hasta_formateado)) {
            // Con filtro de fechas
            $hasta = date('Y-m-d', strtotime($hasta_formateado . ' +1 day'));
            $desde_display = date('d-m-Y', strtotime($desde));
            $hasta_display = date('d-m-Y', strtotime($hasta_formateado));

            $datos = $audit->get_audit_sesiones_x_fecha($desde, $hasta);
            Reportes::get_audit_sesiones_x_fecha($datos, $desde_display, $hasta_display);
        } else {
            // Sin filtro — trae todos
            $datos = $audit->get_audit_sesiones(); // tu método sin filtro
            Reportes::get_audit_sesiones_x_fecha($datos, 'Todos', 'los registros');
        }
        break;

    case 'get_audit_proyectos_x_fecha':
        $desde = $_GET['desde'] ?? '';
        $hasta_formateado = $_GET['hasta'] ?? '';

        if (!empty($desde) && !empty($hasta_formateado)) {
            $hasta = date('Y-m-d', strtotime($hasta_formateado . ' +1 day'));
            $desde_display = date('d-m-Y', strtotime($desde));
            $hasta_display = date('d-m-Y', strtotime($hasta_formateado));

            $datos = $audit->get_auditoria_proyectos_x_fecha($desde, $hasta);
            Reportes::get_audit_proyectos_x_fecha($datos, $desde_display, $hasta_display);
        } else {
            $datos = $audit->get_auditoria_proyectos();
            Reportes::get_audit_proyectos_x_fecha($datos, 'Todos', 'los registros');
        }
        break;

    default:
        echo "Endpoint no reconocido";
        http_response_code(404);
        exit;
        break;
}
