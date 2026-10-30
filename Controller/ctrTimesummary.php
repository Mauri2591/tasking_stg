<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Model/Clases/HtmlPurifier.php";
require_once __DIR__ . "/../Model/Timesummary.php";
require_once __DIR__ . "/../Model/Clases/Validaciones.php";
require_once __DIR__ . "/../Model/Clases/Headers.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";

Headers::validar_sesion();

$timesummary = new timesummary;

switch ($_GET['accion']) {

    case 'insert_tarea':
        $hora_desde = $_POST['hora_desde'] ?? null;
        $hora_hasta = $_POST['hora_hasta'] ?? null;

        // NORMALIZACIÓN DE FECHA
        $fecha_raw = $_POST['fecha'] ?? null;
        if (!$fecha_raw) {
            http_response_code(400);
            echo json_encode(["error" => "Fecha obligatoria"]);
            exit;
        }

        try {
            $fecha_mysql = (new DateTime($fecha_raw))->format('Y-m-d');
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => "Formato de fecha inválido"]);
            exit;
        }

        $data = [
            "producto" => $_POST['id_producto'] ?? null,
            "id_tarea" => $_POST['id_tarea'] ?? null,
            "fecha"    => $fecha_mysql,
            "desde"    => $hora_desde,
            "hasta"    => $hora_hasta
        ];

        if (!timesummary::validarHora($hora_desde) || !timesummary::validarHora($hora_hasta)) {
            http_response_code(400);
            echo json_encode(["error" => "Formato de hora inválido. Use HH:MM"]);
            exit;
        }

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
        $horas_consumidas = $validacion_horas['duracion'];

        if ($timesummary->existe_solapamiento($_SESSION['usu_id'], $fecha_mysql, $hora_desde, $hora_hasta)) {
            http_response_code(409);
            echo json_encode(["error" => "Ya existe una tarea en ese rango horario"]);
            exit;
        }
        $conn = $timesummary->get_conexion();

        $id_proyecto = (int)($_POST['id_proyecto_gestionado'] ?? 0);
        $id_producto = (int)($_POST['id_producto'] ?? 0);
        $id_tarea    = (int)($_POST['id_tarea'] ?? 0);
        $es_telecom  = ($_POST['es_telecom'] ?? '') === 'Telecom';

        $existe = function ($sql, $params) use ($conn) {
            $s = $conn->prepare($sql);
            $s->execute($params);
            return (bool)$s->fetchColumn();
        };

        $error = $timesummary->validar_carga($_SESSION['usu_id'], $id_proyecto, $id_producto, $id_tarea, $es_telecom);

        if ($error) {
            http_response_code(400);
            echo json_encode(["error" => $error]);
            exit;
        }

        try {
            $timesummary->insert_tarea(
                $_SESSION['usu_id'],
                $id_proyecto,
                $id_producto,
                $id_tarea,
                $es_telecom ? 'Telecom' : '',
                (int) ($_POST['id_pm_calidad'] ?? 0),
                $fecha_mysql,
                $hora_desde,
                $hora_hasta,
                $_POST['descripcion'] ?? '',
                $horas_consumidas
            );
            http_response_code(200);
            echo json_encode(["success" => "Tarea creada correctamente"]);
        } catch (Throwable $e) {
            error_log("insert_tarea: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se pudo crear la tarea"]);
        }
        break;

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
                'producto' => $r['producto'] ?? '',

                // PINTAR SI ES TELECOM
                'es_telecom' => $r['es_telecom'] === 'Telecom'
            ];
        }

        echo json_encode($eventos);
        break;

    case 'get_titulos_proyectos':
        header('Content-Type: text/html; charset=UTF-8');

        $datos = $timesummary->get_titulos_proyectos($_SESSION['usu_id']);
        $datosTelecom = $timesummary->getDatosTelecom();
        $htmlOption = '';

        if (!empty($datosTelecom)) {
            foreach ($datosTelecom as $telecom) {
                $idCliente = htmlspecialchars($telecom['client_id'], ENT_QUOTES, 'UTF-8');
                $nombreCliente = htmlspecialchars(mb_strtoupper($telecom['client_rs'], 'UTF-8'), ENT_QUOTES, 'UTF-8');
                $htmlOption .= "<option value=\"$idCliente\" data-pm=\"\">$nombreCliente</option>";
            }
        }

        // Agregar proyectos del usuario
        if (!empty($datos)) {
            foreach ($datos as $val) {
                $idProyecto = htmlspecialchars($val['id_proyecto_gestionado'], ENT_QUOTES, 'UTF-8');
                $idPm = htmlspecialchars($val['id_pm_calidad'] ?? '', ENT_QUOTES, 'UTF-8');
                $titulo = htmlspecialchars($val['titulo'], ENT_QUOTES, 'UTF-8');

                $titulo .= !empty($val['posicion_recurrencia'])
                    ? ' - Rec ' . htmlspecialchars($val['posicion_recurrencia'], ENT_QUOTES, 'UTF-8')
                    : '';

                $htmlOption .= "<option value=\"$idProyecto\" data-pm=\"$idPm\">$titulo</option>";
            }
        }
        echo $htmlOption;
        break;

    case 'get_producto_proyectos':
        if ($_SESSION['sector_id'] == '4') {
            $datos = $timesummary->get_producto_proyectos_total();
            $htmlOption = '';
            foreach ($datos as $val) {
                $htmlOption .= '<option value="' . $val['cat_id'] . '">' . $val['cat_nom'] . '</option>';
            }
            echo $htmlOption;
        } else {
            $datos = $timesummary->get_producto_proyectos($_SESSION['sector_id']);
            $htmlOption = '';
            foreach ($datos as $val) {
                $htmlOption .= '<option value="' . $val['cat_id'] . '">' . $val['cat_nom'] . '</option>';
            }
            echo $htmlOption;
        }
        break;

    case 'delete_tarea':
        $id = (int) ($_POST['id'] ?? 0);

        if (!$timesummary->es_carga_del_usuario($id, $_SESSION['usu_id'])) {
            http_response_code(403);
            echo json_encode(["error" => "No tiene permisos sobre esta carga"]);
            exit;
        }

        try {
            $timesummary->delete_tarea($id);
            echo json_encode(["success" => true]);
        } catch (Throwable $e) {
            error_log("delete_tarea: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se pudo eliminar la tarea"]);
        }
        break;

    case 'delete_tareas_por_ids':
        $ids = json_decode($_POST['ids'] ?? '[]', true);

        if (!is_array($ids) || empty($ids) || count($ids) > 100) {
            http_response_code(400);
            echo json_encode(["error" => "Selección de tareas inválida"]);
            exit;
        }

        // Solo enteros positivos
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($i) => $i > 0)));
        if (empty($ids)) {
            http_response_code(400);
            echo json_encode(["error" => "Selección de tareas inválida"]);
            exit;
        }

        try {
            $borradas = $timesummary->delete_tareas_por_ids($_SESSION['usu_id'], $ids);
            echo json_encode(["success" => "Se eliminaron {$borradas} tarea(s)"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al borrar las tareas"]);
        }
        break;

    case 'updateTarea':
        $id         = (int) ($_POST['id'] ?? 0);
        $hora_desde = $_POST['hora_desde'] ?? '';
        $hora_hasta = $_POST['hora_hasta'] ?? '';

        $validacion = timesummary::validar_horas_minutos(substr($hora_desde, 0, 5), substr($hora_hasta, 0, 5));
        if (!$validacion['success']) {
            http_response_code(400);
            echo json_encode(["error" => $validacion['error']]);
            exit;
        }

        if (!$timesummary->es_carga_del_usuario($id, $_SESSION['usu_id'])) {
            http_response_code(403);
            echo json_encode(["error" => "No tiene permisos sobre esta carga"]);
            exit;
        }

        $fecha = $timesummary->get_fecha_carga($id);
        if ($timesummary->existe_solapamiento($_SESSION['usu_id'], $fecha, $hora_desde, $hora_hasta, $id)) {
            http_response_code(409);
            echo json_encode(["error" => "Ya existe una tarea en ese rango horario"]);
            exit;
        }

        try {
            $ok = $timesummary->updateTarea($id, $hora_desde, $hora_hasta, $_POST['id_tarea'] ?? null, $_POST['descripcion'] ?? '');
            if ($ok === false) {
                http_response_code(400);
                echo json_encode(["error" => "No se pudo actualizar la tarea"]);
                exit;
            }
            echo json_encode(["success" => "Actualizado correctamente"]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error de update"]);
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
        $htmlTable = '';
        $e = function ($v) {
            return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
        };
        // Luego agrego el resto según la lógica
        if ($_SESSION['sector_id'] == "4") {
            foreach ($datos as $val) {
                $htmlTable .= '
            <tr>
                <td title="' . $e($val['titulo']) . '" class="px-1 text-center">' . $e($val['titulo']) . '</td>
                <td class="px-1 text-center">' . $e($val['periodo']) . '</td>
                <td title="' . $e($val['producto']) . '" class="px-1 text-center">' . $e(substr($val['producto'] ?? '', 0, 10)) . '</td>
                <td class="px-1 text-center bg-light" style="border:.1rem solid gainsboro">' . $e($val['hs_dimensionadas']) . '</td>
                <td class="px-1 text-center bg-light fw-bold" style="border:.1rem solid gainsboro">' . $e($val['horas_consumidas']) . '</td>
                ' . (
                    $val['comparacion_horas'] == "HORAS_TOTAL_MENOR_QUE_DIM"
                    ? '<td class="px-1 text-center bg-light fw-bold text-success"  style="border:.1rem solid gainsboro">' . $e($val['horas_total']) . '</td>'
                    : '<td class="px-1 text-center bg-light fw-bold text-danger"  style="border:.1rem solid gainsboro">' . $e($val['horas_total']) . '</td>'
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
                    <td title="' . $e($val['titulo']) . '" class="px-1 text-center">' . $e($val['titulo']) . '</td>
                    <td class="px-1 text-center">' . $e($val['periodo']) . '</td>
                    <td title="' . $e($val['producto']) . '" class="px-1 text-center">' . $e(substr($val['producto'] ?? '', 0, 10)) . '</td>
                    <td class="px-1 text-center bg-light" style="border:.1rem solid gainsboro">' . $e($val['hs_dimensionadas']) . '</td>
                    <td class="px-1 text-center bg-light fw-bold" style="border:.1rem solid gainsboro">' . $e($val['horas_consumidas']) . '</td>
                    ' . (
                        $val['comparacion_horas'] == "HORAS_TOTAL_MENOR_QUE_DIM"
                        ? '<td class="px-1 text-center fw-bold text-success bg-light" style="border:.1rem solid gainsboro">' . $e($val['horas_total']) . '</td>'
                        : '<td class="px-1 text-center fw-bold text-danger bg-light" style="border:.1rem solid gainsboro">' . $e($val['horas_total']) . '</td>'
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
                        <td class="px-3 text-center">' . $val['periodo'] . '</td>
                        <td class="px-3 text-center">' . $val['producto'] . '</td>
                        <td class="px-3 text-center">' . $val['hs_dimensionadas'] . '</td>
                        <td class="px-3 text-center">' . $val['mis_horas'] . '</td>
                        <td class="px-3 text-center">' . $val['horas_total'] . '</td>
                        <td class="px-3 text-center"><a type="button" title="Desea inactivar esta tarea?" onclick="editarTarea(' . $val['id_timesummary_estados'] . ')" class="ri-edit-fill text-danger"></a></td>
                    </tr>          
            ';
        }
        echo $htmlTable;
        break;

    case 'get_titulos_proyectos_total':
        $mostrar_historico = isset($_POST['mostrar_historico']) ? (int)$_POST['mostrar_historico'] : 0;

        $datos = $timesummary->get_titulos_proyectos_total(
            $_SESSION['usu_id'],
            $mostrar_historico
        );
        $data = [];
        if ($_SESSION['sector_id'] == "4") {
            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $row['titulo'];
                $sub_array[] = '<p class="text-center">' . $row['referencia'] . '</p>';
                $sub_array[] = '<span class="badge bg-light px-2 text-dark fs-10 d-inline-block text-start">'
                    . $row['inicio_fin'] .
                    '</span>';
                $sub_array[] = '<p class="text-center p-0 m-0"><span class="badge fs-10 bg-light text-dark">' . $row['hs_dimensionadas'] . '</span></p>';
                $sub_array[] = '<span class="text-center badge border border-dark bg-light text-dark">' . $row['producto'] . '</span>';
                $sub_array[] = $row['es_pm'] == "SI"
                    ? '<span class="badge text-center border border-dark fs-9 fw-bold text-dark">PM</span>'
                    : '<span class="text-center badge bg-danger text-light">Asignado</span>';
                $sub_array[] = $row['est'] == 1
                    ? '<span class="text-center badge bg-success text-light"> Activo </span>'
                    : '<span class="badge" style="background-color:gray;color:white"> Inactivo </span>';
                $sub_array[] = '<a type="button" title="Desea inactivar esta tarea?" onclick="cambiarEstadoTareaHistorial(' . $row['id_timesummary_estados'] . ')" class="ri-edit-fill text-danger fs-14"></a>';

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
                    $sub_array[] = '<p class="text-center">' . $row['referencia'] . '</p>';
                    $sub_array[] = '<span class="badge bg-light px-2 text-dark fs-10 d-inline-block text-start">'
                        . $row['inicio_fin'] .
                        '</span>';
                    $sub_array[] = '<p class="text-center p-0 m-0"><span class="badge fs-10 bg-light text-dark">' . $row['hs_dimensionadas'] . '</span></p>';
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
        $id_timesummary_estados = (int) ($_POST['id_timesummary_estados'] ?? 0);
        $est = (int) ($_POST['est'] ?? -1);

        if ($id_timesummary_estados <= 0 || !in_array($est, [0, 1], true)) {
            http_response_code(400);
            echo json_encode(["Error" => "Datos inválidos"]);
            exit;
        }

        try {
            // El modelo filtra por el usuario de la sesión
            $timesummary->cambiar_estado_tarea(
                $id_timesummary_estados,
                $est,
                $_SESSION['usu_id']
            );

            http_response_code(200);
            echo json_encode(["Success" => "Tarea actualizada correctamente"]);
        } catch (Throwable $e) {
            error_log("cambiar_estado_tarea: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(["Error" => "No se pudo cambiar el estado de la tarea"]);
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
        $usu_id = (int) ($_POST['usu_id'] ?? 0);

        if ($usu_id <= 0) {
            echo json_encode(["sEcho" => 1, "iTotalRecords" => 0, "iTotalDisplayRecords" => 0, "aaData" => []]);
            exit;
        }

        $permitido = false;
        if ($_SESSION['sector_id'] == '4') {
            $permitido = true;
        } elseif (($_SESSION['lider'] ?? '') == 'SI') {
            // El líder solo puede ver usuarios de su propio sector
            $usuarios_sector = array_column($timesummary->get_usuarios_por_sector($_SESSION['sector_id']), 'usu_id');
            $permitido = in_array($usu_id, array_map('intval', $usuarios_sector), true);
        }

        if (!$permitido) {
            http_response_code(403);
            echo json_encode(["error" => "Sin permisos"]);
            exit;
        }

        $e = fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
        $wrap = fn($v) => str_replace("\n", "<br>", $e(wordwrap((string)($v ?? ''), 30, "\n", true)));

        $datos = $timesummary->get_tareas_x_usuario($usu_id);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $wrap($row['cliente']);
            $sub_array[] = $e($row['referencia']);
            $sub_array[] = $e($row['producto']);
            $sub_array[] = $e($row['tarea']);
            $sub_array[] = date('d-m-Y', strtotime($row['fecha']));
            $sub_array[] = $e($row['hora_desde']);
            $sub_array[] = $e($row['hora_hasta']);
            $sub_array[] = $e($row['horas_consumidas']);
            $sub_array[] = $wrap($row['descripcion']);
            $data[] = $sub_array;
        }
        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
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
        $id = (int) ($_POST['id'] ?? 0);

        if (!$timesummary->es_carga_del_usuario($id, $_SESSION['usu_id'])) {
            http_response_code(403);
            echo json_encode(["error" => "No tiene permisos sobre esta carga"]);
            exit;
        }

        echo json_encode($timesummary->getDatosParaEventDrop($id));
        break;

    case 'verificar_conflictos_rango':
        $fechas = json_decode($_POST['fechas'], true);
        $hora_desde = $_POST['hora_desde'] ?? null;
        $hora_hasta = $_POST['hora_hasta'] ?? null;
        $usu_id = $_SESSION['usu_id'] ?? null;

        if (!$fechas || !$hora_desde || !$hora_hasta || !$usu_id) {
            http_response_code(400);
            echo json_encode(["error" => "Parámetros incompletos"]);
            exit;
        }

        $conflictos = $timesummary->verificar_conflictos_rango($fechas, $hora_desde, $hora_hasta, $usu_id);
        echo json_encode(['conflictos' => $conflictos]);
        break;

    case 'get_horas_extras_x_usu':
        $mes    = intval($_POST['mes']  ?? date('n'));
        $anio   = intval($_POST['anio'] ?? date('Y'));
        $usu_id = $_SESSION['usu_id'];

        $data = $timesummary->get_horas_extras_x_usu($usu_id, $mes, $anio);

        $registros = [];
        foreach ($data as $row) {
            $registros[] = [
                $row['fecha'],
                $row['total_dia'],
                $row['horas_extras']
            ];
        }

        echo json_encode([
            "draw"            => intval($_POST['draw'] ?? 1),
            "recordsTotal"    => count($registros),
            "recordsFiltered" => count($registros),
            "data"            => $registros
        ]);
        break;

    case 'getProyectosStatus':
        echo json_encode($timesummary->getProyectosStatus($_SESSION['sector_id']));
        break;

    default:
        http_response_code(404);
        echo json_encode(["Error" => "Acción no reconocida"]);
        break;
}
