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
            $sub_array[] = $row['est'] == 1 ? '<span class="bg-info text-light badge">Activo</span>' : '<span class="badge" style="background-color:gray;color:#fff">Inactivo</span>';
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
        $audit->insert_audit_estados_proyecto($_POST['id_proyecto_gestionado'], $_POST['estados_id'], $_SESSION['usu_id'], $_SESSION['sector_id']);
        break;

    case 'get_auditoria_proyectos':
        $datos = $audit->get_auditoria_proyectos();
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = strtoupper($row['titulo']);
            $sub_array[] = strtoupper($row['refProy']);
            $sub_array[] = strtolower($row['usu_correo']);
            $sub_array[] = strtoupper($row['sector_nombre']);

            $color  = !empty($row['color_estado']) ? $row['color_estado'] : '#F59E0B';
            $icono  = !empty($row['icono'])        ? trim($row['icono'])   : 'ri-chat-new-fill';
            $evento = htmlspecialchars($row['evento']);

            $sub_array[] = '<span class="badge" style="background-color:' . $color . ';color:#fff;">'
                . $evento
                . '</span>' . '<i class="' . $icono . '" style="margin-right:4px; font-size:1rem; color:' . $color . '"></i>';

            $sub_array[] = $row['fecha'];
            $sub_array[] = $row['est'] == 1
                ? '<span class="badge bg-info text-light"><i class="ri-user-follow-fill" style="margin-right:4px;"></i>Activo</span>'
                : '<span class="badge" style="background-color:#475569;color:#fff;"><i class="ri-user-unfollow-fill" style="margin-right:4px;"></i>Inactivo</span>';
            $sub_array[] = $row['fecha_orden']; // columna oculta para ordenar

            $data[] = $sub_array;
        }
        $results = array(
            "sEcho"                => 1,
            "iTotalRecords"        => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData"               => $data
        );
        echo json_encode($results);
        break;

    case 'get_auditoria_proyectos_x_id':
        $datos = $audit->get_auditoria_proyectos_x_id($_POST['id']);
        $data = array();
        $total = count($datos);
        foreach ($datos as $key => $row) {
            $sub_array = array();
            $sub_array[] = $total - $key;
            $sub_array[] = strtoupper($row['titulo']);
            $sub_array[] = strtoupper($row['refProy']);
            $sub_array[] = strtolower($row['usu_correo']);
            $sub_array[] = strtoupper($row['sector_nombre']);

            $color  = !empty($row['color_estado']) ? $row['color_estado'] : '#F59E0B';
            $icono  = !empty($row['icono'])        ? trim($row['icono'])   : 'ri-chat-new-fill';
            $evento = htmlspecialchars($row['evento']);

            $sub_array[] = '<span class="badge" style="background-color:' . $color . ';color:#fff;">'
                . $evento
                . '</span>' . '<i class="' . $icono . '" style="margin-right:4px; font-size:1rem; color:' . $color . '"></i>';

            $sub_array[] = $row['fecha'];
            $sub_array[] = $row['est'] == 1
                ? '<span class="badge bg-info text-light"><i class="ri-user-follow-fill" style="margin-right:4px;"></i>Activo</span>'
                : '<span class="badge" style="background-color:#475569;color:#fff;"><i class="ri-user-unfollow-fill" style="margin-right:4px;"></i>Inactivo</span>';

            $data[] = $sub_array;
        }
        $results = array(
            "sEcho"                => 1,
            "iTotalRecords"        => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData"               => $data
        );
        echo json_encode($results);
        break;

    default:
        echo "Case no valido";
        break;
}
