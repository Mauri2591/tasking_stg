<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Auditoria.php";
$audit = new Auditoria();
switch ($_GET['case']) {
    case 'get_audit_sesiones':
        $datos = $audit->get_audit_sesiones();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = strtolower($row['usu_correo']);
            $sub_array[] = strtoupper($row['sector_nombre']);
            $sub_array[] = $row['fecha'];
            $sub_array[] = $row['evento'] == "LOGIN"
                ? '<span><span class="badge bg-success text-light">Login</span> <i class="fs-18 text-success ri-login-circle-fill"></i></span>'
                : '<span><span class="badge bg-warning text-light">Logout</span> <i class="fs-18 text-warning ri-logout-circle-fill"></i></span>';
            $sub_array[] = $row['estado_usuario'] == 1 ? '<span class="bg-info text-light badge">Activo</span>' : '<span class="badge" style="background-color:gray;color:#fff">Inactivo</span>';
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

    case 'insert_audit_estados_proyecto':
        $audit->insert_audit_estados_proyecto($_POST['id_proyecto_gestionado'],$_POST['estados_id'],$_SESSION['usu_id'],$_SESSION['sector_id']);
        break;

    default:
        echo "Case no valido";
        break;
}
