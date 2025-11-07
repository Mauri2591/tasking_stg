<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Clientes.php";
$clientes = new Clientes();
switch ($_GET['cliente']) {
    case 'total':
        $datos = $clientes->get_clientes();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = strlen($row['client_rs']) > 40
                ? '<span class="badge bg-light text-primary border border-primary" data-placement="top" title="' . $row['client_rs'] . '">' . substr($row['client_rs'], 0, 38) . '...</span>'
                : '<span class="badge bg-light text-primary border border-primary" data-placement="top" title="' . $row['client_rs'] . '">' . $row['client_rs'] . '</span>';
            $sub_array[] = strlen($row['client_cuit']) > 10
                ? '<span class="badge bg-light text-dark" data-placement="top" title="' . $row['client_cuit'] . '">' . substr($row['client_cuit'], 0, 38) . '...</span>'
                : '<span class="badge bg-light text-dark" data-placement="top" title="' . $row['client_cuit'] . '">' . $row['client_cuit'] . '</span>';
            $sub_array[] = strlen($row['client_correo']) > 9
                ? '<span class="badge bg-light text-dark" data-placement="top" title="' . $row['client_correo'] . '">' . substr($row['client_correo'], 0, 7) . '...</span>'
                : '<span class="badge bg-light text-dark" data-placement="top" title="' . $row['client_correo'] . '">' . $row['client_correo'] . '</span>';
            $sub_array[] = strlen($row['client_tel']) > 12
                ? '<span class="badge bg-light text-dark" data-placement="top" title="' . $row['client_tel'] . '">' . substr($row['client_tel'], 0, 12) . '...</span>'
                : '<span class="badge bg-light text-dark" data-placement="top" title="' . $row['client_tel'] . '">' . $row['client_tel'] . '</span>';
            $sub_array[] = '<span onclick=altaProject(' . $row['client_id'] . ') type="button" data-placement="top" title="Crear Proyecto" data-bs-toggle="modal" data-bs-target="#ModalAltaProject"><i class="ri-send-plane-fill text-primary fs-18"></i></span>';
            $sub_array[] = '<span onclick=verProyectosPorCliente(' . $row['client_id'] . ') type="button" data-placement="top" title="Ver Proyectos" data-bs-toggle="modal" data-bs-target="#ModalProyectosPorCliente"><i class="  ri-database-2-fill text-secondary fs-18"></i></span>';
            $sub_array[] = '<span onclick=editCliente(' . $row['client_id'] . ') type="button" data-placement="top" title="Editar Cliente" data-bs-toggle="modal" data-bs-target="#ModalUpdateCliente"><i class=" ri-edit-2-fill text-success fs-18"></i></span>';
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

    case 'insert_cliente':
        if (empty($_POST['client_rs'])) {
            echo json_encode(["messaje" => "Campo vacio"]);
            http_response_code(400);
            exit();
        } else {
            $clientes->insert_cliente($_POST['client_rs'], $_POST['pais_id'], $_POST['client_cuit'], $_POST['client_correo'], $_POST['client_tel']);
            http_response_code(200);
        }
        break;

    case 'get_paise_x_id':
        echo json_encode($clientes->get_paise_x_id($_POST['client_id']));
        break;

    case 'get_datos_cliente':
        echo json_encode($clientes->get_datos_cliente($_POST['client_id']));
        break;

    case 'update_datos_cliente':
        if (empty($_POST['client_rs'])) {
            http_response_code(400);
            echo json_encode(["messaje" => "Campo vacio"]);
            exit();
        } else {
            $clientes->update_datos_cliente($_POST['client_id'], $_POST['client_rs'], $_POST['client_cuit'], $_POST['client_correo'], $_POST['client_tel']);
            http_response_code(200);
        }
        break;

    case 'get_sectores':
        $data = $clientes->get_sectores();
        $option = '';
        foreach ($data as $key => $val) {
            $option .= '<label> <input id="sector_id" type="checkbox" value="' . $val['sector_id'] . '">' . $val['sector_nombre'] . '</label>' . "<br>";
        }
        echo $option;
        break;

    default:
        # code...
        break;
}
