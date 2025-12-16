<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Model/Clases/HtmlPurifier.php";
require_once __DIR__ . "/../Model/Timesummary.php";
require_once __DIR__ . "/../Model/Clases/Validaciones.php";
require_once __DIR__ . "/../Model/Clases/Headers.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";

$timesummary = new timesummary;

switch ($_GET['accion']) {

    case 'insert_tarea':
        $hora_desde = $_POST['hora_desde'] ?? null;
        $hora_hasta = $_POST['hora_hasta'] ?? null;

        $data = [
            "proyecto" => $_POST['id_proyecto_gestionado'] ?? null,
            "producto" => $_POST['id_producto'] ?? null,
            "id_tarea" => $_POST['id_tarea'] ?? null,
            "es_telecom" => $_POST['es_telecom'] ?? null,
            "fecha" => $_POST['fecha'] ?? null,
            "desde" => $hora_desde,
            "hasta" => $hora_hasta
        ];

        // Validación de formato de hora
        if (!timesummary::validarHora($hora_desde) || !timesummary::validarHora($hora_hasta)) {
            http_response_code(400);
            echo json_encode(["error" => "Formato de hora inválido. Use HH:MM"]);
            exit;
        }
        // Validación de campos vacíos
        if (!timesummary::validarDatosVacios($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Hay campos obligatorios vacíos"]);
            exit;
        }

        $validacion_horas = timesummary::validar_horas_minutos($hora_desde, $hora_hasta);

        if (!$validacion_horas['success']) {
            http_response_code(400);
            echo json_encode(["error" => $validacion_horas['error']]);
            exit;
        }

        $horas_consumidas = $validacion_horas['duracion']; // Ejemplo: "01:30"

        try {
            $timesummary->insert_tarea(
                $_SESSION['usu_id'] ?? null,
                $_POST['id_proyecto_gestionado'] ?? null,
                $_POST['id_producto'] ?? null,
                $_POST['id_tarea'] ?? null,
                $_POST['es_telecom'] ?? null,
                $_POST['id_pm_calidad'] ?? null,
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
        $data = $timesummary->get_tareas($_SESSION['usu_id'] ?? null);
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
        header('Content-Type: text/html; charset=UTF-8');

        $datos = $timesummary->get_titulos_proyectos($_SESSION['usu_id']);
        $datosTelecom = $timesummary->getDatosTelecom();
        $htmlOption = '';

        // Agregar siempre Telecom primero
        if (!empty($datosTelecom)) {
            foreach ($datosTelecom as $telecom) {
                $idCliente = htmlspecialchars($telecom['client_id'], ENT_QUOTES, 'UTF-8');
                $nombreCliente = strtoupper(htmlspecialchars($telecom['client_rs'], ENT_QUOTES, 'UTF-8'));
                $htmlOption .= "<option value=\"$idCliente\" data-pm=\"\">$nombreCliente</option>";
            }
        }

        // Agregar proyectos del usuario
        if (!empty($datos)) {
            foreach ($datos as $val) {
                $idProyecto = htmlspecialchars($val['id_proyecto_gestionado'], ENT_QUOTES, 'UTF-8');
                $idPm = htmlspecialchars($val['id_pm_calidad'] ?? '', ENT_QUOTES, 'UTF-8');
                $titulo = htmlspecialchars($val['titulo'], ENT_QUOTES, 'UTF-8');

                $htmlOption .= "<option value=\"$idProyecto\" data-pm=\"$idPm\">$titulo</option>";
            }
        } else {
            $htmlOption .= '';
        }

        echo $htmlOption;
        break;

    case 'get_producto_proyectos':
        $datos = $timesummary->get_producto_proyectos($_SESSION['sector_id']);
        $htmlOption = '';
        foreach ($datos as $val) {
            $htmlOption .= '<option value="' . $val['cat_id'] . '">' . $val['cat_nom'] . '</option>';
        }
        echo $htmlOption;
        break;

    case 'delete_tarea':
        try {
            $timesummary->delete_tarea($_POST['id']);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;

    case 'updateTarea':
        try {
            $timesummary->updateTarea($_POST['id'], $_POST['hora_desde'], $_POST['hora_hasta'], $_POST['id_tarea'], $_POST['descripcion']);
            echo json_encode(["success" => "Actualizado correctamente"]);
        } catch (\Throwable $e) {
            echo "Error de update: " . $e->getMessage() . "\nCodigo: " . $e->getCode();
            exit;
        }
        break;

    case 'get_cat_id_by_proyecto_gestionado':
        echo json_encode($timesummary->get_cat_id_by_proyecto_gestionado($_POST['id']));
        break;

    case 'get_tareas_total';
        $datos = $timesummary->get_tareas_total();
        $htmlOption = '';
        foreach ($datos as $key => $val) {
            $htmlOption .= '<option value="' . $val['id'] . '" title="' . htmlspecialchars($val['definicion']) . '">' . $val['nombre'] . '</option>';
        }
        echo $htmlOption;
        break;

    case 'get_tareas_x_id':
        // 1. Obtengo la tarea guardad
        $tareaActual = $timesummary->get_tareas_x_id($_POST['id']);
        $id_tarea_seleccionada = !empty($tareaActual[0]['id_tarea']) ? $tareaActual[0]['id_tarea'] : null;

        // 2. Obtener todas las tareas posibles
        $todas = $timesummary->get_tareas_total();

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
        $datos = $timesummary->get_titulos_proyectos($_SESSION['usu_id']);
        $datosTelecom = $timesummary->getDatosTelecom();
        $htmlTable = '';
        // Primero agregamos datosTelecom
        foreach ($datosTelecom as $telecom) {
            $htmlTable .= '
            <br>
            <tr>
            <td class="px-1 text-center">' . htmlspecialchars($telecom['client_rs']) . '</td>
            <td class="px-1 text-center">TELECOM</td>
            <td class="px-1 text-center bg-light" style="border:.1rem solid gainsboro">-</td>
            <td class="px-1 text-center bg-light" style="border:.1rem solid gainsboro">-</td>
            <td class="px-1 text-center bg-light" style="border:.1rem solid gainsboro">-</td>
        </tr>';
        }

        // Luego agregamos el resto según la lógica
        if ($_SESSION['sector_id'] == "4") {
            foreach ($datos as $val) {
                $htmlTable .= '
            <tr>
                <td class="px-1 text-center">' . substr($val['titulo'], 0, 30) . '</td>
                <td class="px-1 text-center">' . substr($val['producto'], 0, 10) . '</td>
                <td class="px-1 text-center bg-light" style="border:.1rem solid gainsboro">' . $val['hs_dimensionadas'] . '</td>
                <td class="px-1 text-center bg-light fw-bold" style="border:.1rem solid gainsboro">' . $val['horas_consumidas'] . '</td>
                ' . (
                    $val['comparacion_horas'] == "HORAS_TOTAL_MENOR_QUE_DIM"
                    ? '<td class="px-1 text-center bg-light fw-bold text-success"  style="border:.1rem solid gainsboro">' . $val['horas_total'] . '</td>'
                    : '<td class="px-1 text-center bg-light fw-bold text-danger"  style="border:.1rem solid gainsboro">' . $val['horas_total'] . '</td>'
                ) . '
            </tr>';
            }
        } else {
            $proyectos_mostrados = [];
            foreach ($datos as $val) {
                $id_proyecto = $val['id_proyecto_gestionado'];
                if (!in_array($id_proyecto, $proyectos_mostrados)) {
                    $htmlTable .= '
                <tr>
                    <td class="px-1 text-center">' . substr($val['titulo'], 0, 30) . '</td>
                    <td class="px-1 text-center">' . substr($val['producto'], 0, 10) . '</td>
                    <td class="px-1 text-center bg-light" style="border:.1rem solid gainsboro">' . $val['hs_dimensionadas'] . '</td>
                    <td class="px-1 text-center bg-light fw-bold" style="border:.1rem solid gainsboro">' . $val['horas_consumidas'] . '</td>
                    ' . (
                        $val['comparacion_horas'] == "HORAS_TOTAL_MENOR_QUE_DIM"
                        ? '<td class="px-1 text-center fw-bold text-success bg-light" style="border:.1rem solid gainsboro">' . $val['horas_total'] . '</td>'
                        : '<td class="px-1 text-center fw-bold text-danger bg-light" style="border:.1rem solid gainsboro">' . $val['horas_total'] . '</td>'
                    ) . '
                </tr>';
                    $proyectos_mostrados[] = $id_proyecto;
                }
            }
        }

        echo $htmlTable;
        break;



    case 'get_validar_si_hay_tareas_activas':
        $data = $timesummary->get_validar_si_hay_tareas_activas($_SESSION['usu_id']);
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
        $datos = $timesummary->get_titulos_proyectos_like($_SESSION['usu_id'], $_POST['titulo']);
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
        $datos = $timesummary->get_titulos_proyectos_total($_SESSION['usu_id']);
        if ($_SESSION['sector_id'] == "4") {
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row['titulo'];
                $sub_array[] = '<p class="text-center p-0 m-0"><span class="badge border border-dark bg-light text-dark">' . $row['hs_dimensionadas'] . '</span></p>';
                $sub_array[] = '<span class="text-center badge border border-dark bg-light text-dark">' . $row['producto'] . '</span>';
                $sub_array[] = $row['es_pm'] == "SI"
                    ? '<span class="badge text-center border border-dark fs-9 fw-bold text-dark">PM</span>'
                    : '<span class="text-center badge bg-danger text-light">Asignado</span>';
                $sub_array[] = $row['est'] == 1
                    ? '<span class="text-center badge border border-success text-success"> Activo </span>'
                    : '<span class="badge" style="background-color:gray;color:white"> Inactivo </span>';
                $sub_array[] = '<a type="button" title="Desea inactivar esta tarea?" onclick="cambiarEstadoTareaHistorial(' . $row['id_timesummary_estados'] . ')" class="ri-edit-fill text-secondary fs-14"></a>';

                $data[] = $sub_array;
            }
        } else {
            $data = array();
            $proyectos_vistos = [];

            foreach ($datos as $row) {
                // Evita duplicados por id_proyecto_gestionado
                if (!in_array($row['id_proyecto_gestionado'], $proyectos_vistos)) {
                    $sub_array = array();
                    $sub_array[] = $row['titulo'];
                    $sub_array[] = '<p class="text-center p-0 m-0"><span class="badge border border-dark bg-light text-dark">' . $row['hs_dimensionadas'] . '</span></p>';
                    $sub_array[] = '<span class="text-center badge border border-dark bg-light text-dark">' . $row['producto'] . '</span>';
                    $sub_array[] = $row['est'] == 1
                        ? '<span class="text-center badge bg-success text-light"> Activo </span>'
                        : '<span class="badge" style="background-color:gray;color:white"> Inactivo </span>';
                    $sub_array[] = '<a type="button" title="Desea inactivar esta tarea?" onclick="cambiarEstadoTareaHistorial(' . $row['id_timesummary_estados'] . ')" class="ri-edit-fill text-danger"></a>';

                    $data[] = $sub_array;
                    $proyectos_vistos[] = $row['id_proyecto_gestionado'];
                }
            }
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
        $datos = $timesummary->get_estado_tarea($_POST['id_timesummary_estados']);
        echo json_encode($datos);
        break;


    case 'cambiar_estado_tarea':
        try {
            // ✅ Obtener el usuario actual desde la sesión
            $usuario_asignado = $_SESSION['usu_id'];

            // ✅ Llamada al modelo con el filtro por usuario
            $timesummary->cambiar_estado_tarea(
                $_POST['id_timesummary_estados'],
                $_POST['est'],
                $usuario_asignado
            );

            http_response_code(200);
            echo json_encode(["Success" => "Tarea actualizada correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["Error" => "Error SQL: " . $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["Error" => "Error general: " . $e->getMessage()]);
        }
        break;

    case 'get_sectores':
        $data = $timesummary->get_sectores();
        foreach ($data as $val) {
            // marcar activo el sector ETHICAL HACKING (id = 1)
            $activeClass = ($val['sector_id'] == 1) ? 'active' : '';
            $ariaSelected = ($val['sector_id'] == 1) ? 'true' : 'false';
            $tabId = strtolower(str_replace(' ', '_', $val['sector_nombre']));
        ?>
            <li class="nav-item">
                <a class="py-1 px-2 nav-link <?php echo $activeClass; ?>"
                    data-sector-id="<?php echo $val['sector_id']; ?>"
                    data-bs-toggle="tab"
                    href="#tab_<?php echo $tabId; ?>"
                    role="tab"
                    aria-selected="<?php echo $ariaSelected; ?>">
                    <?php echo htmlspecialchars($val['sector_nombre'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </li>
            <?php
        }
        break;

    case 'get_usuarios_por_sector':
        $sector_id = $_POST['sector_id'];
        $data = $timesummary->get_usuarios_por_sector($sector_id);

        if (empty($data)) {
            echo "<span class='text-muted'>No hay usuarios en este sector.</span>";
        } else {
            foreach ($data as $val) {
            ?>
                <span type="button" onclick="verTareasUsuario(<?php echo $val['usu_id'] ?>)" class="me-2 bg-primary badge border border-secondary text-light">
                    <?php echo htmlspecialchars($val['usu_nom'] . ' ' . $val['usu_ape'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
<?php
            }
        }
        break;

    case 'get_tareas_x_usuario':
        $datos = $timesummary->get_tareas_x_usuario($_POST['usu_id']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $cliente = $row['cliente'];
            if (strlen($cliente) > 30) {
                $cliente = wordwrap($cliente, 30, "<br>", true);
            }
            $sub_array[] = $cliente;
            $sub_array[] = $row['referencia'];
            $sub_array[] = $row['producto'];
            $sub_array[] = $row['tarea'];
            $sub_array[] = date('d-m-Y', strtotime($row['fecha']));
            $sub_array[] = $row['hora_desde'];
            $sub_array[] = $row['hora_hasta'];
            $sub_array[] = $row['horas_consumidas'];
            $descripcion = $row['descripcion'];
            if (strlen($descripcion) > 30) {
                $descripcion = wordwrap($descripcion, 30, "<br>", true);
            }

            $sub_array[] = $descripcion;
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

    case 'get_tareas_x_usuario_x_usu_id':
        $datos = $timesummary->get_tareas_x_usuario_x_usu_id($_SESSION['usu_id']);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row['cliente'];
            $sub_array[] = $row['referencia'];
            $sub_array[] = $row['producto'];
            $sub_array[] = $row['tarea'];
            $sub_array[] = date('d-m-Y', strtotime($row['fecha']));
            $sub_array[] = $row['hora_desde'];
            $sub_array[] = $row['hora_hasta'];
            $sub_array[] = $row['horas_consumidas'];
            $sub_array[] = $row['descripcion'];
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

    case 'getNombreCliente':
        echo json_encode($timesummary->getNombreCliente($_POST['client_rs']));
        break;

    case 'getDatosParaEventDrop':
        $datos=$timesummary->getDatosParaEventDrop($_POST['id']);
        echo json_encode($datos);
        break;

    default:
        http_response_code(404);
        echo json_encode(["Error" => "Acción no reconocida"]);
        break;
}
