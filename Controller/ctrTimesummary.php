<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Model/Clases/HtmlPurifier.php";
require_once __DIR__ . "/../Model/Tymesummary.php";
require_once __DIR__ . "/../Model/Clases/Validaciones.php";
require_once __DIR__ . "/../Model/Clases/Headers.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";

$tymesummary = new Tymesummary;

switch ($_GET['accion']) {

    case 'insert_tarea':
        $hora_desde = $_POST['hora_desde'] ?? null;
        $hora_hasta = $_POST['hora_hasta'] ?? null;

        $data = [
            "proyecto" => $_POST['id_proyecto_gestionado'] ?? null,
            "producto" => $_POST['id_producto'] ?? null,
            "producto" => $_POST['id_tarea'] ?? null,
            "fecha" => $_POST['fecha'] ?? null,
            "desde" => $hora_desde,
            "hasta" => $hora_hasta
        ];

        // Validación de formato de hora
        if (!Tymesummary::validarHora($hora_desde) || !Tymesummary::validarHora($hora_hasta)) {
            http_response_code(400);
            echo json_encode(["error" => "Formato de hora inválido. Use HH:MM"]);
            exit;
        }
        // Validación de campos vacíos
        if (!Tymesummary::validarDatosVacios($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Hay campos obligatorios vacíos"]);
            exit;
        }

        $validacion_horas = Tymesummary::validar_horas_minutos($hora_desde, $hora_hasta);

        if (!$validacion_horas['success']) {
            http_response_code(400);
            echo json_encode(["error" => $validacion_horas['error']]);
            exit;
        }

        $horas_consumidas = $validacion_horas['duracion']; // Ejemplo: "01:30"

        try {
            $tymesummary->insert_tarea(
                $_SESSION['usu_id'] ?? null,
                $_POST['id_proyecto_gestionado'] ?? null,
                $_POST['id_producto'] ?? null,
                $_POST['id_tarea'] ?? null,
                $_POST['fecha'] ?? null,
                $_POST['hora_desde'] ?? null,
                $_POST['hora_hasta'] ?? null,
                $_POST['descripcion'] ?? null,
                $horas_consumidas
            );

            http_response_code(200);
            echo json_encode(["success" => "Tarea agregada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode([
                "error" => "Error SQL",
                "message" => $e->getMessage(),
                "sqlstate" => $e->getCode()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Error general",
                "message" => $e->getMessage()
            ]);
        }
        break;


    // OBTENER TAREAS PARA FULLCALENDAR
    case 'get_tareas':
        $data = $tymesummary->get_tareas($_SESSION['usu_id'] ?? null);
        $eventos = [];
        foreach ($data as $r) {
            $eventos[] = [
                'id' => $r['id'],
                'title' => $r['titulo_proyecto'],
                'start' => $r['fecha'] . 'T' . $r['hora_desde'],
                'end' => $r['fecha'] . 'T' . $r['hora_hasta'],
                'descripcion' => $r['descripcion'] ?? '',
                'id_proyecto_gestionado' => $r['id_proyecto_gestionado'] ?? 0,
                'id_producto' => $r['id_producto'] ?? 0,
                'id_tarea' => $r['id_tarea'] ?? 0,
                'fecha' => $r['fecha'] ?? null,
                'producto' => $r['producto'] ?? ''
            ];
        }

        echo json_encode($eventos);
        break;

    case 'get_titulos_proyectos':
        $datos = $tymesummary->get_titulos_proyectos($_SESSION['usu_id']);
        $htmlOption = '';
        foreach ($datos as $val) {
            $htmlOption .= '<option value="' . $val['id_proyecto_gestionado'] . '">' . $val['titulo'] . '</option>';
        }
        echo $htmlOption;
        break;

    case 'get_producto_proyectos':
        $datos = $tymesummary->get_producto_proyectos($_SESSION['sector_id']);
        $htmlOption = '';
        foreach ($datos as $val) {
            $htmlOption .= '<option value="' . $val['cat_id'] . '">' . $val['cat_nom'] . '</option>';
        }
        echo $htmlOption;
        break;

    case 'get_tareas_total';
        $datos = $tymesummary->get_tareas_total();
        $htmlOption = '';
        foreach ($datos as $key => $val) {
            $htmlOption .= '<option value="' . $val['id'] . '" title="' . htmlspecialchars($val['definicion']) . '">' . $val['nombre'] . '</option>';
        }
        echo $htmlOption;
        break;

    case 'get_tareas_x_id':
        // 1. Obtengo la tarea guardad
        $tareaActual = $tymesummary->get_tareas_x_id($_POST['id']);
        $id_tarea_seleccionada = !empty($tareaActual[0]['id_tarea']) ? $tareaActual[0]['id_tarea'] : null;

        // 2. Obtener todas las tareas posibles
        $todas = $tymesummary->get_tareas_total();

        // 3. Construir el <option> con la seleccionada
        $htmlOption = '';
        foreach ($todas as $val) {
            $selected = ($val['id'] == $id_tarea_seleccionada) ? ' selected' : '';
            $htmlOption .= '<option value="' . $val['id'] . '" title="' . htmlspecialchars($val['definicion']) . '"' . $selected . '>'
                . htmlspecialchars($val['nombre']) . '</option>';
        }
        echo $htmlOption;
        break;

    case 'datos_tabla_ts':
        $datos = $tymesummary->get_titulos_proyectos($_SESSION['usu_id']);
        $htmlTable = '';
        foreach ($datos as $key => $val) {
            $htmlTable .= '

                    <tr>
                        <td class="px-3 text-center">' . substr($val['titulo'], 0, 20) . '</td>
                        <td class="px-3 text-center">' . $val['producto'] . '</td>
                        <td class="px-3 text-center">' . $val['hs_dimensionadas'] . '</td>
                        <td class="px-3 text-center"><a type="button" title="Desea inactivar esta tarea?" onclick="editarTarea(' . $val['id_timesummary_estados'] . ')" class="ri-edit-fill text-danger"></a></td>
                    </tr>          
            ';
        }
        echo $htmlTable;
        break;

    case 'get_validar_si_hay_tareas_activas':
        $data = $tymesummary->get_validar_si_hay_tareas_activas($_SESSION['usu_id']);
        if ($data->total > 0) {
?>
            <span class="badge bg-success text-light">Proyectos activos</span>
        <?php
        } else {
        ?>
            <span class="badge" style="background-color: gray;color:white;">Sin proyectos activos</span>
<?php
        }
        break;

    case 'get_titulos_proyectos_like':
        $datos = $tymesummary->get_titulos_proyectos_like($_SESSION['usu_id'], $_POST['titulo']);
        $htmlTable = '';
        foreach ($datos as $key => $val) {
            $htmlTable .= '
                    <tr>
                        <td class="px-3 text-center">' . substr($val['titulo'], 0, 20) . '</td>
                        <td class="px-3 text-center">' . $val['producto'] . '</td>
                        <td class="px-3 text-center">' . $val['hs_dimensionadas'] . '</td>
                        <td class="px-3 text-center"><a type="button" title="Desea inactivar esta tarea?" onclick="editarTarea(' . $val['id_timesummary_estados'] . ')" class="ri-edit-fill text-danger"></a></td>
                    </tr>          
            ';
        }
        echo $htmlTable;
        break;

    case 'get_titulos_proyectos_total':
        $datos = $tymesummary->get_titulos_proyectos_total($_SESSION['usu_id']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['titulo'];
            $sub_array[] = '<p class="text-center p-0 m-0"><span class="badge border border-dark bg-light text-dark">' . $row['hs_dimensionadas'] . '</span></p>';
            $sub_array[] = '<span class="text-center badge border border-dark bg-light text-dark">' . $row['producto'] . '</span>';
            $sub_array[] = $row['est'] == 1 ? '<span class="text-center badge bg-success text-light"> Activo </span>' : '<span class="badge" style="background-color:gray;color:white"> Inactivo </span>';
            $sub_array[] = '<a type="button" title="Desea inactivar esta tarea?" onclick="cambiarEstadoTareaHistorial(' . $row['id_timesummary_estados'] . ')" class="ri-edit-fill text-danger"></a>';

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

    case 'get_estado_tarea':
        $datos = $tymesummary->get_estado_tarea($_POST['id_timesummary_estados']);
        echo json_encode($datos);
        break;

    case 'cambiar_estado_tarea':
        try {
            $tymesummary->cambiar_estado_tarea($_POST['id_timesummary_estados'], $_POST['est']);
            http_response_code(200);
            echo json_encode(["Success" => "Tarea inactivada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["Error" => "Error SQL: " . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["Error" => "Error general: " . $e->getMessage()]);
        }
        break;


    default:
        http_response_code(404);
        echo json_encode(["Error" => "Acción no reconocida"]);
        break;
}
