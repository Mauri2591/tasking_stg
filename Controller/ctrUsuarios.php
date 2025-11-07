<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Usuarios.php";
$usuarios = new Usuarios();
switch ($_GET['usuarios']) {
    case 'get_usuarios':
        $datos = $usuarios->get_usuarios();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['usu_nom'];
            $sub_array[] = $row['usu_correo'];
            $sub_array[] = $row['usu_tel'];
            $sub_array[] = '<span class="badge badge-soft-primary ms-auto">' . $row['sector'] . '</span>';
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

    case 'editarPerfil':
        $usu_pass = $_POST['usu_pass'];
        if (!empty($usu_pass)) {
            $usuarios->editarPerfil($_SESSION['usu_id'], $usu_pass);
            echo json_encode(["Success" => "Password guardada"]);
        } else {
            echo json_encode(["Error" => "Password vacia"]);
            http_response_code(400);
        }
        break;

    case 'get_sectores':
        $data = $usuarios->get_sectores();
        $option = '';
        foreach ($data as $key => $val) {
            $option .= '<option value="' . $val['sector_id'] . '">' . $val['sector_nombre'] . '</option>';
        }
        echo $option;
        break;

    case 'insert_usuario':
        if (
            isset($_POST['usu_nom'], $_POST['usu_correo'], $_POST['usu_tel'], $_POST['sector_id']) &&
            !empty($_POST['usu_nom']) &&
            !empty($_POST['usu_correo']) &&
            !empty($_POST['usu_tel']) &&
            !empty($_POST['sector_id'])
        ) {
            $rol_id = $_POST['sector_id'] == "4" ? 1 : 2;
            $password_default = "111";
            $data = $usuarios->insert_usuario(
                $_SESSION['usu_id'],
                $_POST['usu_nom'],
                $_POST['usu_correo'],
                password_hash($password_default, PASSWORD_DEFAULT),
                $_POST['usu_tel'],
                $_POST['sector_id'],
                $rol_id
            );
            http_response_code(201);
            echo json_encode(["Success" => "Usuario creado correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["Error" => "Datos vacios o incompletos"]);
            exit;
        }
        break;
    default:
        # code...
        break;
}

