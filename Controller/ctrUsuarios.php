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

    case 'get_usuario_x_id':
        echo json_encode($usuarios->get_usuario_x_id($_SESSION['usu_id']));
        break;

    case 'editarPerfil':
        $usu_nom    = trim($_POST['usu_nom'] ?? '');
        $usu_ape    = trim($_POST['usu_ape'] ?? '');
        $usu_correo = trim($_POST['usu_correo'] ?? '');
        $validarPass = $_POST['idCheckValidarUsuPass'] ?? 'NO';
        $validarPass2 = $_POST['idCheckValidarUsuPass2'] ?? 'NO';
        $password   = trim($_POST['password'] ?? '');

        // Validar campos comunes
        if (empty($usu_nom) || empty($usu_ape) || empty($usu_correo)) {
            http_response_code(400);
            echo json_encode(["Error" => "Nombre, apellido y correo son obligatorios."]);
            exit;
        }

        if ($validarPass === "SI") {
            if (empty($password)) {
                http_response_code(400);
                echo json_encode(["Error" => "El campo password es obligatorio."]);
                exit;
            }

            // Actualiza TODO, incluida la password
            $usuarios->editarPerfil($_SESSION['usu_id'], $usu_nom, $usu_ape, $usu_correo, $password);
        } else {
            // Actualiza todo MENOS la password
            $usuarios->editarPerfil($_SESSION['usu_id'], $usu_nom, $usu_ape, $usu_correo, null);
        }

        if ($validarPass2 === "SI") {
            if (empty($password)) {
                http_response_code(400);
                echo json_encode(["Error" => "El campo password es obligatorio."]);
                exit;
            }

            // Actualiza TODO, incluida la password
            $usuarios->editarPerfil($_SESSION['usu_id'], $usu_nom, $usu_ape, $usu_correo, $password);
        } else {
            // Actualiza todo MENOS la password
            $usuarios->editarPerfil($_SESSION['usu_id'], $usu_nom, $usu_ape, $usu_correo, null);
        }

        echo json_encode(["Success" => "Perfil actualizado correctamente."]);
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

    // Tomo datos
    $usu_nom    = $_POST['usu_nom']    ?? '';
    $usu_ape    = $_POST['usu_ape']    ?? '';
    $usu_correo = $_POST['usu_correo'] ?? '';
    $usu_tel    = $_POST['usu_tel']    ?? '';
    $sector_id  = $_POST['sector_id']  ?? 0;

    // Chequeo mínimo (SIN ROMPERTE LOS HUEVOS)
    if ($usu_nom === '' || $usu_correo === '') {
        http_response_code(400);
        echo json_encode(["error" => "Datos vacios"]);
        exit;
    }

    // Insert
    $usuarios->insert_usuario(
        $_SESSION['usu_id'],
        $usu_nom,
        $usu_ape,
        $usu_correo,
        password_hash("111", PASSWORD_DEFAULT),
        $usu_tel,
        (int)$sector_id,
        2
    );

    echo json_encode(["Success" => "OK"]);
    break;




    default:
        break;
}
