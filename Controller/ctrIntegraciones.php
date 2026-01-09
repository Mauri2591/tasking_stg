<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Integraciones.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";
$integracion = new Integraciones();
switch ($_GET['case']) {

    case 'get_herramientas':
        $datos = $integracion->get_herramientas();
        $htmlOption = '';
        foreach ($datos as $key => $val) {
            $htmlOption .= '<option value=' . $val['id'] . '>' . $val['herramienta'] . '</option>';
        }
        echo $htmlOption;
        break;

    case 'get_api_key':
        if (empty($_POST['sector_id']) || empty($_POST['id_herramienta'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "Datos incompletos"
            ]);
            exit;
        }

        $sector_id = (int) $_POST['sector_id'];

        $tieneActiva = $integracion->existe_api_key_activa_x_sector($sector_id);
        if ($tieneActiva) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "mensaje" => "El sector ya posee una API Key activa"
            ]);
            exit;
        }
        $api_key_plana = 'key_' . bin2hex(random_bytes(32));

        $api_key_plana = 'key_' . bin2hex(random_bytes(32));
        $api_key_cifrada = Openssl::set_ssl_encrypt($api_key_plana);

        $ok = $integracion->crear_api_key(
            $api_key_cifrada,
            $sector_id,
            (int) $_POST['id_herramienta'],
            $_SESSION['usu_id']
        );
        if ($ok) {
            http_response_code(201);
            echo json_encode([
                "status"  => "success",
                "api_key" => $api_key_plana,
                "mensaje" => "API Key creada correctamente"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se pudo crear la API Key"
            ]);
        }
        exit;


    case 'get_api_keys':
        $datos = $integracion->get_api_keys();
        $data = array();
        $colores_prioridad = array("BAJO" => "badge border border-success text-success", "MEDIO" => "badge border border-warning text-warning", "ALTO" => "badge border border-danger text-danger");
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = Openssl::get_ssl_decrypt($row['api_key']);
            $sub_array[] = $row['herramienta'];
            $sub_array[] = $row['sector'];
            $sub_array[] = $row['usu_crea'];
            $sub_array[] = '<span title="Eliminar Api Key" type="button" onclick=inactivarApiKey(' . $row['id'] . ')><i class=" ri-delete-bin-5-fill text-danger fs-16"></i></span>';
            $data[] = $sub_array;
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'inhabilitar_api_keys':
        $datos = $integracion->inhabilitar_api_keys($_POST['id']);
        if ($datos > 0) {
            echo json_encode(["status" => "success", "mensaje" => "Inhabilitado correctamente"]);
            http_response_code(201);
            exit;
        } else {
            echo json_encode(["status" => "error", "mensaje" => "No se pudo inhabilitar"]);
            http_response_code(401);
            exit;
        }
        break;

    default:
        # code...
        break;
}
