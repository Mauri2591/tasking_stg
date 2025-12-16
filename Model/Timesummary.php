<?php

class timesummary extends Conexion
{

    public function get_tareas($usu_id = null)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT
                t.id,
                t.fecha,
                t.hora_desde,
                t.hora_hasta,
                CASE WHEN es_telecom = 'Telecom' THEN 'TELECOM' ELSE p.titulo END AS titulo_proyecto,
                t.descripcion,
                t.id_proyecto_gestionado,
                t.id_producto,
                tareas.id AS id_tarea,
                tareas.nombre AS nombre_tarea,
                t.id_pm_calidad,
                tm_categoria.cat_nom AS producto
            FROM timesummary_carga t
            LEFT JOIN proyecto_gestionado p ON t.id_proyecto_gestionado = p.id
            LEFT JOIN tm_categoria ON t.id_producto = tm_categoria.cat_id
            LEFT JOIN tareas ON t.id_tarea = tareas.id
            WHERE t.usu_id = :usu_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function validarHora($hora)
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $hora);
    }

    public static function validarDatosVacios($datos)
    {
        foreach ($datos as $key => $val) {
            if (empty($val) && !in_array($key, ['proyecto', 'es_telecom'])) {
                return false;
            }
        }
        return true;
    }
    public static function validar_horas_minutos($hora_desde, $hora_hasta)
    {
        // Validar formato HH:MM
        if (!self::validarHora($hora_desde) || !self::validarHora($hora_hasta)) {
            return [
                "success" => false,
                "error" => "Formato no válido. Debe ser HH:MM"
            ];
        }

        // Convertir a timestamp
        $inicio = strtotime($hora_desde);
        $fin = strtotime($hora_hasta);

        if ($inicio >= $fin) {
            return [
                "success" => false,
                "error" => "La hora de inicio debe ser menor que la hora de finalización"
            ];
        }

        $diferencia_seg = $fin - $inicio;
        $horas = floor($diferencia_seg / 3600);
        $minutos = floor(($diferencia_seg % 3600) / 60);

        // Formatear salida tipo HH:MM
        $duracion = sprintf('%02d:%02d', $horas, $minutos);

        // Retornar validación exitosa y duración calculada
        return [
            "success" => true,
            "duracion" => $duracion
        ];
    }

    public function insert_tarea($usu_id, $id_proyecto_gestionado, $id_producto, $id_tarea, $es_telecom, $id_pm_calidad, $fecha, $hora_desde, $hora_hasta, $descripcion, $horas_consumidas)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO timesummary_carga
        (usu_id, id_proyecto_gestionado, id_producto,id_tarea, es_telecom, id_pm_calidad, fecha, hora_desde, hora_hasta, descripcion,horas_consumidas)
        VALUES (:usu_id, :id_proyecto_gestionado, :id_producto,:id_tarea, :es_telecom, :id_pm_calidad, :fecha, :hora_desde, :hora_hasta, :descripcion,:horas_consumidas)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt->bindValue(":id_tarea", $id_tarea, PDO::PARAM_INT);
        $stmt->bindValue(":es_telecom", $es_telecom, PDO::PARAM_STR);
        $stmt->bindValue(":id_pm_calidad", $id_pm_calidad, PDO::PARAM_INT);
        $stmt->bindValue(":fecha", htmlentities($fecha, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":hora_desde", htmlentities($hora_desde, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":hora_hasta", htmlentities($hora_hasta, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", htmlentities($descripcion, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":horas_consumidas", $horas_consumidas, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function delete_tarea($id)
    {
        $conn = parent::get_conexion();
        $sql = "DELETE FROM timesummary_carga WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function updateTarea($id, $hora_desde, $hora_hasta, $id_tarea, $descripcion)
    {
        $conn = parent::get_conexion();

        // Calcular diferencia en formato HH:MM
        $horaDesde = new DateTime($hora_desde);
        $horaHasta = new DateTime($hora_hasta);
        $intervalo = $horaDesde->diff($horaHasta);

        // Formato HH:MM (ejemplo: 01:30)
        $horasConsumidas = str_pad($intervalo->h, 2, "0", STR_PAD_LEFT) . ":" .
            str_pad($intervalo->i, 2, "0", STR_PAD_LEFT);

        $sql = "UPDATE timesummary_carga 
            SET hora_desde = :hora_desde, 
                hora_hasta = :hora_hasta, 
                id_tarea = :id_tarea,
                descripcion = :descripcion,
                horas_consumidas = :horas_consumidas
            WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", (int)$id, PDO::PARAM_INT);
        $stmt->bindValue(":hora_desde", $hora_desde, PDO::PARAM_STR);
        $stmt->bindValue(":hora_hasta", $hora_hasta, PDO::PARAM_STR);
        $stmt->bindValue(":id_tarea", $id_tarea, PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(":horas_consumidas", $horasConsumidas, PDO::PARAM_STR);
        try {
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en updateTarea: " . $e->getMessage());
            return false;
        }
    }

    public function getDatosTelecom()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT client_id, client_rs FROM clientes WHERE clientes.client_id=209";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en updateTarea: " . $e->getMessage());
            return false;
        }
    }

    public function get_titulos_proyectos($usu_asignado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    tse.id AS id_timesummary_estados,
    pg.id AS id_proyecto_gestionado,
    pg.titulo,
    tm_categoria.cat_nom AS producto,
    IFNULL(dim.total_hs_dimensionadas, 0) AS hs_dimensionadas,

    -- horas_consumidas (solo las que el usuario actual cargó)
    IFNULL((
        SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(tc1.horas_consumidas))), '%H:%i')
        FROM timesummary_carga tc1
        WHERE tc1.id_proyecto_gestionado = pg.id
          AND tc1.usu_id = tse.usuario_asignado
          AND tc1.est = 1
    ), '00:00') AS horas_consumidas,

    -- horas_total (suma general según grupo PM o no PM)
    CASE 
        WHEN tse.id_pm_calidad IS NOT NULL THEN 
            IFNULL((
                SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(tc2.horas_consumidas))), '%H:%i')
                FROM timesummary_carga tc2
                WHERE tc2.id_proyecto_gestionado = pg.id
                  AND tc2.est = 1
                  AND tc2.id_pm_calidad = tse.id_pm_calidad
            ), '00:00')
        ELSE
            IFNULL((
                SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(tc3.horas_consumidas))), '%H:%i')
                FROM timesummary_carga tc3
                WHERE tc3.id_proyecto_gestionado = pg.id
                  AND tc3.est = 1
                  AND (tc3.id_pm_calidad IS NULL OR tc3.id_pm_calidad = 0)
            ), '00:00')
    END AS horas_total,

    -- comparacion_horas (solo contra el grupo del usuario)
    IF(
        (
            SELECT SUM(TIME_TO_SEC(tc4.horas_consumidas))
            FROM timesummary_carga tc4
            WHERE tc4.id_proyecto_gestionado = pg.id
              AND tc4.est = 1
              AND (
                   (tse.id_pm_calidad IS NOT NULL AND tc4.id_pm_calidad = tse.id_pm_calidad)
                OR (tse.id_pm_calidad IS NULL AND (tc4.id_pm_calidad IS NULL OR tc4.id_pm_calidad = 0))
              )
        ) > (IFNULL(dim.total_hs_dimensionadas,0) * 3600),
        'HORAS_TOTAL_MAYOR_QUE_DIM',
        'HORAS_TOTAL_MENOR_QUE_DIM'
    ) AS comparacion_horas,

    tse.est,
    tse.id_pm_calidad,
    CASE WHEN tse.id_pm_calidad IS NOT NULL THEN 'SI' ELSE 'NO' END AS es_pm

FROM proyecto_gestionado pg
LEFT JOIN tm_estados e 
    ON pg.estados_id = e.estados_id
LEFT JOIN tm_categoria 
    ON pg.cat_id = tm_categoria.cat_id
INNER JOIN timesummary_estados tse 
    ON pg.id = tse.id_proyecto_gestionado
   AND tse.usuario_asignado = :usu_asignado
   AND tse.est = 1
LEFT JOIN (
    SELECT 
        id_proyecto_gestionado,
        SUM(hs_dimensionadas) AS total_hs_dimensionadas
    FROM dimensionamiento
    GROUP BY id_proyecto_gestionado
) AS dim 
    ON dim.id_proyecto_gestionado = pg.id
WHERE e.estados_id IN (1, 2, 3, 4)
GROUP BY 
    tse.id, pg.id, pg.titulo, tm_categoria.cat_nom, dim.total_hs_dimensionadas, tse.est, tse.id_pm_calidad
ORDER BY pg.titulo";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_asignado", $usu_asignado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_titulos_proyectos_total($usu_asignado)
    {
        $conexion = parent::get_conexion();
        $sql = "SELECT 
                tse.id AS id_timesummary_estados,
                tse.id_pm_calidad,
                pg.id AS id_proyecto_gestionado,
                pg.titulo,
                tm_categoria.cat_nom AS producto,
                IFNULL(dim.total_hs_dimensionadas, 0) AS hs_dimensionadas,
                tse.est,
                CASE 
                    WHEN tse.id_pm_calidad IS NOT NULL THEN 'SI'
                    ELSE 'NO'
                END AS es_pm
            FROM proyecto_gestionado pg
            LEFT JOIN tm_estados e 
                ON pg.estados_id = e.estados_id
            LEFT JOIN tm_categoria 
                ON pg.cat_id = tm_categoria.cat_id
            INNER JOIN timesummary_estados tse 
                ON pg.id = tse.id_proyecto_gestionado
                AND tse.usuario_asignado = :usu_asignado
            LEFT JOIN (
                SELECT id_proyecto_gestionado, SUM(hs_dimensionadas) AS total_hs_dimensionadas
                FROM dimensionamiento
                GROUP BY id_proyecto_gestionado
            ) AS dim ON dim.id_proyecto_gestionado = pg.id
            WHERE e.estados_id IN (1, 2, 3, 4)
            ORDER BY pg.titulo";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':usu_asignado', $usu_asignado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_estado_tarea($id_timesummary_estados)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT timesummary_estados.est AS estado FROM timesummary_estados WHERE 
        timesummary_estados.id=:id_timesummary_estados";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_timesummary_estados", $id_timesummary_estados, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function get_titulos_proyectos_like($usu_asignado, $titulo)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tse.id AS id_timesummary_estados, pg.id AS id_proyecto_gestionado, 
        pg.titulo, up.usu_asignado, SUM(d.hs_dimensionadas) AS hs_dimensionadas, 
        tm_categoria.cat_nom AS producto, tse.est FROM proyecto_gestionado pg 
        LEFT JOIN usuario_proyecto up ON pg.id = up.id_proyecto_gestionado 
        LEFT JOIN tm_estados e ON pg.estados_id = e.estados_id 
        LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado 
        LEFT JOIN tm_categoria ON pg.cat_id = tm_categoria.cat_id 
        LEFT JOIN timesummary_estados tse ON pg.id = tse.id_proyecto_gestionado 
        AND tse.est = 1 WHERE pg.titulo LIKE CONCAT('%', :titulo, '%') AND up.usu_asignado = :usu_asignado
        AND e.estados_id IN (1, 2, 3, 4) AND tse.est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_asignado", $usu_asignado, PDO::PARAM_INT);
        $stmt->bindValue(":titulo", htmlentities($titulo, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_validar_si_hay_tareas_activas($usu_asignado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT COUNT(*) AS total
        FROM timesummary_estados
        WHERE usuario_asignado = :usuario_asignado AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usuario_asignado", $usu_asignado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_producto_proyectos($sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT *
            FROM tm_categoria
            WHERE (sector_id = :sector_id AND est = 1)
            OR cat_id = 26";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cat_id_by_proyecto_gestionado($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT cat_id FROM proyecto_gestionado WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cambiar_estado_tarea($id_timesummary_estados, $est, $usuario_asignado)
    {
        try {
            $conexion = parent::get_conexion();
            $query = "UPDATE timesummary_estados 
                  SET est = :est 
                  WHERE id = :id_timesummary_estados 
                  AND usuario_asignado = :usuario_asignado";

            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':est', $est, PDO::PARAM_INT);
            $stmt->bindParam(':id_timesummary_estados', $id_timesummary_estados, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_asignado', $usuario_asignado, PDO::PARAM_INT);
            $stmt->execute();

            // ✅ Validación: asegura que solo actualice la tarea del usuario actual
            if ($stmt->rowCount() === 0) {
                throw new Exception("No se encontró una tarea asociada al usuario actual.");
            }

            return true;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
    public function get_tareas_total()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT id,nombre,definicion FROM tareas";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tareas_x_id($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT timesummary_carga.id_tarea FROM timesummary_carga WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sectores()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT sector_id, sector_nombre FROM sectores WHERE est=1 AND sector_id != 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_usuarios_todos_los_sectores()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT usu_id, usu_nom, usu_ape, usu_correo, sectores.sector_id, sectores.sector_nombre FROM tm_usuario INNER JOIN sectores ON tm_usuario.sector_id=sectores.sector_id WHERE tm_usuario.est=1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_usuarios_por_sector($sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT usu_id, usu_nom, usu_ape, usu_correo 
            FROM tm_usuario 
            WHERE est=1 AND sector_id = ? AND usu_id != 82"; //que no traiga al usuario dev
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sector_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tareas_x_usuario($usu_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
            tm_categoria.cat_nom AS producto,
            tareas.nombre AS tarea,
            timesummary_carga.fecha,
            TIME_FORMAT(timesummary_carga.hora_desde, '%H:%i') AS hora_desde,
            TIME_FORMAT(timesummary_carga.hora_hasta, '%H:%i') AS hora_hasta,
            TIME_FORMAT(
                TIMEDIFF(timesummary_carga.hora_hasta, timesummary_carga.hora_desde),
                '%H:%i'
            ) AS horas_consumidas,
            timesummary_carga.descripcion,
            timesummary_carga.fech_crea AS fecha_carga,

            CASE 
                WHEN timesummary_carga.es_telecom = 'Telecom'
                    THEN 'TELECOM'
                ELSE clientes.client_rs
            END AS cliente,

            proyecto_gestionado.refProy AS referencia,
            tm_usuario.usu_nom,
            tm_usuario.usu_ape

            FROM timesummary_carga

            INNER JOIN tm_usuario 
                ON timesummary_carga.usu_id = tm_usuario.usu_id

            LEFT JOIN tm_categoria 
                ON timesummary_carga.id_producto = tm_categoria.cat_id

            LEFT JOIN tareas 
                ON timesummary_carga.id_tarea = tareas.id

            LEFT JOIN proyecto_gestionado 
                ON timesummary_carga.id_proyecto_gestionado = proyecto_gestionado.id

            LEFT JOIN proyecto_cantidad_servicios 
                ON proyecto_gestionado.id_proyecto_cantidad_servicios = proyecto_cantidad_servicios.id

            LEFT JOIN proyectos 
                ON proyecto_cantidad_servicios.proy_id = proyectos.proy_id

            LEFT JOIN clientes 
                ON proyectos.client_id = clientes.client_id

            WHERE timesummary_carga.usu_id = :usu_id
            ORDER BY timesummary_carga.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tareas_x_usuario_x_usu_id($usu_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
            tm_categoria.cat_nom AS producto,
            tareas.nombre AS tarea,
            timesummary_carga.fecha,
            TIME_FORMAT(timesummary_carga.hora_desde, '%H:%i') AS hora_desde,
            TIME_FORMAT(timesummary_carga.hora_hasta, '%H:%i') AS hora_hasta,
            TIME_FORMAT(
                TIMEDIFF(timesummary_carga.hora_hasta, timesummary_carga.hora_desde),
                '%H:%i'
            ) AS horas_consumidas,
            timesummary_carga.descripcion,
            timesummary_carga.fech_crea AS fecha_carga,

            CASE 
                WHEN timesummary_carga.es_telecom = 'Telecom'
                    THEN 'TELECOM'
                ELSE clientes.client_rs
            END AS cliente,

            proyecto_gestionado.refProy AS referencia,
            tm_usuario.usu_nom,
            tm_usuario.usu_ape

        FROM timesummary_carga

        INNER JOIN tm_usuario 
            ON timesummary_carga.usu_id = tm_usuario.usu_id

        LEFT JOIN tm_categoria 
            ON timesummary_carga.id_producto = tm_categoria.cat_id

        LEFT JOIN tareas 
            ON timesummary_carga.id_tarea = tareas.id

        LEFT JOIN proyecto_gestionado 
            ON timesummary_carga.id_proyecto_gestionado = proyecto_gestionado.id

        LEFT JOIN proyecto_cantidad_servicios 
            ON proyecto_gestionado.id_proyecto_cantidad_servicios = proyecto_cantidad_servicios.id

        LEFT JOIN proyectos 
            ON proyecto_cantidad_servicios.proy_id = proyectos.proy_id

        LEFT JOIN clientes 
            ON proyectos.client_id = clientes.client_id

        WHERE timesummary_carga.usu_id = :usu_id

        ORDER BY timesummary_carga.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNombreCliente($client_rs)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT clientes.client_id AS cliente_id, clientes.client_rs, 
    IF(clientes.est=1,'ACTIVO','INACTIVO') AS estado 
    FROM clientes 
    WHERE clientes.client_rs LIKE :client_rs";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":client_rs", "%$client_rs%", PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDatosReporteSinFiltro()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT
            clientes.client_id,
            clientes.client_rs,
            COUNT(DISTINCT proyecto_gestionado.id) AS proyectos_gestionados,
            COUNT(DISTINCT proyecto_cantidad_servicios.id) AS total_proyectos_cantidad_servicios,
            proyecto_gestionado.fech_inicio,
            proyecto_gestionado.fech_fin,
            proyecto_gestionado.refProy,
            dimensionamiento.hs_dimensionadas AS dimensionamiento,
            -- hs_restante: lo que falta (nunca negativo)
            CASE 
                WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) >= dimensionamiento.hs_dimensionadas
                THEN '00:00'
                ELSE CONCAT(
                    LPAD(FLOOR(ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2)), 2, '0'), ':',
                    LPAD(ROUND((ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2) - FLOOR(ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2))) * 60), 2, '0')
                )
            END AS hs_restante,

            -- hs_resto: exceso si consumidas > dimensionamiento
            CASE 
                WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) > dimensionamiento.hs_dimensionadas
                THEN CONCAT(
                    LPAD(FLOOR(ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2)), 2, '0'), ':',
                    LPAD(ROUND((ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2) - FLOOR(ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2))) * 60), 2, '0')
                )
                ELSE NULL
            END AS hs_resto,
        CONCAT(tm_usuario_pm.usu_nom, ' ', 
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            )
        ) AS usuario_pm_calidad,
            -- Horas PM (sumadas si id_pm_calidad coincide con pm_calidad.id)
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            ) AS horas_pm,
            -- Horas consumidas normales (sin PM)
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            ) AS horas_consumidas_total,
            GROUP_CONCAT(
                DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN
                    CONCAT(horas_usuarios.usu_nom, ' ',
                        TIME_FORMAT(SEC_TO_TIME(horas_usuarios.total_segundos), '%H:%i')
                    )
                END SEPARATOR ', '
            ) AS horas_consumidas_por_usuario,
            GROUP_CONCAT(DISTINCT tm_usuario.usu_nom SEPARATOR ', ') AS usuarios_asignados,
            tm_categoria.cat_nom AS producto,
            sectores.sector_nombre AS sector,
            MAX(proyecto_gestionado.recurrencia) AS recurrencia,
            MAX(tm_estados.estados_nombre) AS estado,
            proyectos.cantidad_servicios
        FROM proyectos
        INNER JOIN clientes ON proyectos.client_id = clientes.client_id
        LEFT JOIN proyecto_cantidad_servicios ON proyecto_cantidad_servicios.proy_id = proyectos.proy_id
        LEFT JOIN proyecto_gestionado ON proyecto_gestionado.id_proyecto_cantidad_servicios = proyecto_cantidad_servicios.id
        LEFT JOIN tm_categoria ON proyecto_gestionado.cat_id = tm_categoria.cat_id
        LEFT JOIN sectores ON sectores.sector_id = proyecto_gestionado.sector_id
        LEFT JOIN dimensionamiento ON proyecto_gestionado.id = dimensionamiento.id_proyecto_gestionado
        LEFT JOIN usuario_proyecto ON proyecto_gestionado.id = usuario_proyecto.id_proyecto_gestionado
        LEFT JOIN tm_usuario ON tm_usuario.usu_id = usuario_proyecto.usu_asignado
        LEFT JOIN pm_calidad ON pm_calidad.id_proyecto_gestionado = proyecto_gestionado.id
        LEFT JOIN tm_usuario AS tm_usuario_pm ON tm_usuario_pm.usu_id = pm_calidad.usu_crea
        LEFT JOIN (
            SELECT 
                tc.id_proyecto_gestionado,
                tc.usu_id,
                tc.id_pm_calidad,
                tm_usuario.usu_nom,
                SUM(TIME_TO_SEC(tc.horas_consumidas)) AS total_segundos
            FROM timesummary_carga tc
            INNER JOIN tm_usuario ON tm_usuario.usu_id = tc.usu_id
            GROUP BY tc.id_proyecto_gestionado, tc.usu_id, tc.id_pm_calidad, tm_usuario.usu_nom
        ) AS horas_usuarios ON horas_usuarios.id_proyecto_gestionado = proyecto_gestionado.id
        LEFT JOIN tm_estados ON proyecto_gestionado.estados_id = tm_estados.estados_id
        WHERE proyecto_gestionado.estados_id IN (1,2,3,4,14)
        GROUP BY 
            clientes.client_id,
            clientes.client_rs,
            tm_categoria.cat_nom,
            sectores.sector_nombre,
            proyecto_gestionado.fech_inicio,
            proyecto_gestionado.fech_fin,
            dimensionamiento.hs_dimensionadas,
            tm_usuario_pm.usu_nom
        ORDER BY clientes.client_rs";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDatosReporteConFiltroFechas($fecha_desde, $fecha_hasta)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT
        clientes.client_id,
        clientes.client_rs,
        COUNT(DISTINCT proyecto_gestionado.id) AS proyectos_gestionados,
        COUNT(DISTINCT proyecto_cantidad_servicios.id) AS total_proyectos_cantidad_servicios,
        proyecto_gestionado.fech_inicio,
        proyecto_gestionado.fech_fin,
        proyecto_gestionado.refProy,
        dimensionamiento.hs_dimensionadas AS dimensionamiento,
        -- hs_restante: lo que falta (nunca negativo)
        CASE 
            WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) >= dimensionamiento.hs_dimensionadas
            THEN '00:00'
            ELSE CONCAT(
                LPAD(FLOOR(ROUND((
                    dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                ), 2)), 2, '0'), ':',
                LPAD(ROUND((ROUND((
                    dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                ), 2) - FLOOR(ROUND((
                    dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                ), 2))) * 60), 2, '0')
            )
        END AS hs_restante,
        -- hs_resto: exceso si consumidas > dimensionamiento
        CASE 
            WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) > dimensionamiento.hs_dimensionadas
            THEN CONCAT(
                LPAD(FLOOR(ROUND((
                    (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                ), 2)), 2, '0'), ':',
                LPAD(ROUND((ROUND((
                    (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                ), 2) - FLOOR(ROUND((
                    (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                ), 2))) * 60), 2, '0')
            )
            ELSE NULL
        END AS hs_resto,
        CONCAT(tm_usuario_pm.usu_nom, ' ', 
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            )
        ) AS usuario_pm_calidad,
        -- Horas PM (sumadas si id_pm_calidad coincide con pm_calidad.id)
        TIME_FORMAT(
            SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
            '%H:%i'
        ) AS horas_pm,

        -- Horas consumidas normales (sin PM)
        TIME_FORMAT(
            SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END)),
            '%H:%i'
        ) AS horas_consumidas_total,
        GROUP_CONCAT(
            DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN
                CONCAT(horas_usuarios.usu_nom, ' ',
                    TIME_FORMAT(SEC_TO_TIME(horas_usuarios.total_segundos), '%H:%i')
                )
            END SEPARATOR ', '
        ) AS horas_consumidas_por_usuario,
        GROUP_CONCAT(DISTINCT tm_usuario.usu_nom SEPARATOR ', ') AS usuarios_asignados,
        tm_categoria.cat_nom AS producto,
        sectores.sector_nombre AS sector,
        MAX(proyecto_gestionado.recurrencia) AS recurrencia,
        MAX(tm_estados.estados_nombre) AS estado,
        proyectos.cantidad_servicios
        FROM proyectos
        INNER JOIN clientes ON proyectos.client_id = clientes.client_id
        LEFT JOIN proyecto_cantidad_servicios ON proyecto_cantidad_servicios.proy_id = proyectos.proy_id
        LEFT JOIN proyecto_gestionado ON proyecto_gestionado.id_proyecto_cantidad_servicios = proyecto_cantidad_servicios.id
        LEFT JOIN tm_categoria ON proyecto_gestionado.cat_id = tm_categoria.cat_id
        LEFT JOIN sectores ON sectores.sector_id = proyecto_gestionado.sector_id
        LEFT JOIN dimensionamiento ON proyecto_gestionado.id = dimensionamiento.id_proyecto_gestionado
        LEFT JOIN usuario_proyecto ON proyecto_gestionado.id = usuario_proyecto.id_proyecto_gestionado
        LEFT JOIN tm_usuario ON tm_usuario.usu_id = usuario_proyecto.usu_asignado
        LEFT JOIN pm_calidad ON pm_calidad.id_proyecto_gestionado = proyecto_gestionado.id
        LEFT JOIN tm_usuario AS tm_usuario_pm ON tm_usuario_pm.usu_id = pm_calidad.usu_crea
        LEFT JOIN (
            SELECT 
                tc.id_proyecto_gestionado,
                tc.usu_id,
                tc.id_pm_calidad,
                tm_usuario.usu_nom,
                SUM(TIME_TO_SEC(tc.horas_consumidas)) AS total_segundos
            FROM timesummary_carga tc
            INNER JOIN tm_usuario ON tm_usuario.usu_id = tc.usu_id
            GROUP BY tc.id_proyecto_gestionado, tc.usu_id, tc.id_pm_calidad, tm_usuario.usu_nom
        ) AS horas_usuarios ON horas_usuarios.id_proyecto_gestionado = proyecto_gestionado.id
        LEFT JOIN tm_estados ON proyecto_gestionado.estados_id = tm_estados.estados_id
        WHERE proyecto_gestionado.fech_inicio BETWEEN :fecha_desde AND :fecha_hasta AND proyecto_gestionado.estados_id IN(1,2,3,4,14)
        GROUP BY 
            clientes.client_id,
            clientes.client_rs,
            tm_categoria.cat_nom,
            sectores.sector_nombre,
            proyecto_gestionado.fech_inicio,
            proyecto_gestionado.fech_fin,
            dimensionamiento.hs_dimensionadas,
            tm_usuario_pm.usu_nom
        ORDER BY clientes.client_rs";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":fecha_desde", $fecha_desde, PDO::PARAM_STR);
        $stmt->bindValue(":fecha_hasta", $fecha_hasta, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDatosReporteConFiltroPoriDCliente($client_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT
            clientes.client_id,
            clientes.client_rs,
            COUNT(DISTINCT proyecto_gestionado.id) AS proyectos_gestionados,
            COUNT(DISTINCT proyecto_cantidad_servicios.id) AS total_proyectos_cantidad_servicios,
            proyecto_gestionado.fech_inicio,
            proyecto_gestionado.fech_fin,
            proyecto_gestionado.refProy,
            dimensionamiento.hs_dimensionadas AS dimensionamiento,
            -- hs_restante: lo que falta (nunca negativo)
            CASE 
                WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) >= dimensionamiento.hs_dimensionadas
                THEN '00:00'
                ELSE CONCAT(
                    LPAD(FLOOR(ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2)), 2, '0'), ':',
                    LPAD(ROUND((ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2) - FLOOR(ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2))) * 60), 2, '0')
                )
            END AS hs_restante,
            -- hs_resto: exceso si consumidas > dimensionamiento
            CASE 
                WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) > dimensionamiento.hs_dimensionadas
                THEN CONCAT(
                    LPAD(FLOOR(ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2)), 2, '0'), ':',
                    LPAD(ROUND((ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2) - FLOOR(ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2))) * 60), 2, '0')
                )
                ELSE NULL
            END AS hs_resto,
        CONCAT(tm_usuario_pm.usu_nom, ' ', 
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            )
        ) AS usuario_pm_calidad,
            -- Horas PM (sumadas si id_pm_calidad coincide con pm_calidad.id)
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            ) AS horas_pm,
            -- Horas consumidas normales (sin PM)
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            ) AS horas_consumidas_total,
            GROUP_CONCAT(
                DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN
                    CONCAT(horas_usuarios.usu_nom, ' ',
                        TIME_FORMAT(SEC_TO_TIME(horas_usuarios.total_segundos), '%H:%i')
                    )
                END SEPARATOR ', '
            ) AS horas_consumidas_por_usuario,
            GROUP_CONCAT(DISTINCT tm_usuario.usu_nom SEPARATOR ', ') AS usuarios_asignados,
            tm_categoria.cat_nom AS producto,
            sectores.sector_nombre AS sector,
            MAX(proyecto_gestionado.recurrencia) AS recurrencia,
            MAX(tm_estados.estados_nombre) AS estado,
            proyectos.cantidad_servicios
            FROM proyectos
            INNER JOIN clientes ON proyectos.client_id = clientes.client_id
            LEFT JOIN proyecto_cantidad_servicios ON proyecto_cantidad_servicios.proy_id = proyectos.proy_id
            LEFT JOIN proyecto_gestionado ON proyecto_gestionado.id_proyecto_cantidad_servicios = proyecto_cantidad_servicios.id
            LEFT JOIN tm_categoria ON proyecto_gestionado.cat_id = tm_categoria.cat_id
            LEFT JOIN sectores ON sectores.sector_id = proyecto_gestionado.sector_id
            LEFT JOIN dimensionamiento ON proyecto_gestionado.id = dimensionamiento.id_proyecto_gestionado
            LEFT JOIN usuario_proyecto ON proyecto_gestionado.id = usuario_proyecto.id_proyecto_gestionado
            LEFT JOIN tm_usuario ON tm_usuario.usu_id = usuario_proyecto.usu_asignado
            LEFT JOIN pm_calidad ON pm_calidad.id_proyecto_gestionado = proyecto_gestionado.id
            LEFT JOIN tm_usuario AS tm_usuario_pm ON tm_usuario_pm.usu_id = pm_calidad.usu_crea
            LEFT JOIN (
            SELECT 
                tc.id_proyecto_gestionado,
                tc.usu_id,
                tc.id_pm_calidad,
                tm_usuario.usu_nom,
                SUM(TIME_TO_SEC(tc.horas_consumidas)) AS total_segundos
            FROM timesummary_carga tc
            INNER JOIN tm_usuario ON tm_usuario.usu_id = tc.usu_id
            GROUP BY tc.id_proyecto_gestionado, tc.usu_id, tc.id_pm_calidad, tm_usuario.usu_nom
        ) AS horas_usuarios ON horas_usuarios.id_proyecto_gestionado = proyecto_gestionado.id
        LEFT JOIN tm_estados ON proyecto_gestionado.estados_id = tm_estados.estados_id
        WHERE clientes.client_id=:client_id AND proyecto_gestionado.estados_id IN(1,2,3,4,14)
        GROUP BY 
            clientes.client_id,
            clientes.client_rs,
            tm_categoria.cat_nom,
            sectores.sector_nombre,
            proyecto_gestionado.fech_inicio,
            proyecto_gestionado.fech_fin,
            dimensionamiento.hs_dimensionadas,
            tm_usuario_pm.usu_nom
        ORDER BY clientes.client_rs";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":client_id", $client_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getReportePorFechasYCliente($client_id, $fecha_desde, $fecha_hasta)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT
            clientes.client_id,
            clientes.client_rs,
            COUNT(DISTINCT proyecto_gestionado.id) AS proyectos_gestionados,
            COUNT(DISTINCT proyecto_cantidad_servicios.id) AS total_proyectos_cantidad_servicios,
            proyecto_gestionado.fech_inicio,
            proyecto_gestionado.refProy,
            proyecto_gestionado.fech_fin,
            dimensionamiento.hs_dimensionadas AS dimensionamiento,
            -- hs_restante: lo que falta (nunca negativo)
            CASE 
                WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) >= dimensionamiento.hs_dimensionadas
                THEN '00:00'
                ELSE CONCAT(
                    LPAD(FLOOR(ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2)), 2, '0'), ':',
                    LPAD(ROUND((ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2) - FLOOR(ROUND((
                        dimensionamiento.hs_dimensionadas - (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600)
                    ), 2))) * 60), 2, '0')
                )
            END AS hs_restante,
            -- hs_resto: exceso si consumidas > dimensionamiento
            CASE 
                WHEN (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) > dimensionamiento.hs_dimensionadas
                THEN CONCAT(
                    LPAD(FLOOR(ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2)), 2, '0'), ':',
                    LPAD(ROUND((ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2) - FLOOR(ROUND((
                        (SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END) / 3600) - dimensionamiento.hs_dimensionadas
                    ), 2))) * 60), 2, '0')
                )
                ELSE NULL
            END AS hs_resto,
        CONCAT(tm_usuario_pm.usu_nom, ' ', 
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            )
        ) AS usuario_pm_calidad,
            -- Horas PM (sumadas si id_pm_calidad coincide con pm_calidad.id)
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad = pm_calidad.id THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            ) AS horas_pm,
            -- Horas consumidas normales (sin PM)
            TIME_FORMAT(
                SEC_TO_TIME(SUM(DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN horas_usuarios.total_segundos ELSE 0 END)),
                '%H:%i'
            ) AS horas_consumidas_total,
            GROUP_CONCAT(
                DISTINCT CASE WHEN horas_usuarios.id_pm_calidad IS NULL OR horas_usuarios.id_pm_calidad = 0 THEN
                    CONCAT(horas_usuarios.usu_nom, ' ',
                        TIME_FORMAT(SEC_TO_TIME(horas_usuarios.total_segundos), '%H:%i')
                    )
                END SEPARATOR ', '
            ) AS horas_consumidas_por_usuario,
            GROUP_CONCAT(DISTINCT tm_usuario.usu_nom SEPARATOR ', ') AS usuarios_asignados,
            tm_categoria.cat_nom AS producto,
            sectores.sector_nombre AS sector,
            MAX(proyecto_gestionado.recurrencia) AS recurrencia,
            MAX(tm_estados.estados_nombre) AS estado,
            proyectos.cantidad_servicios
            FROM proyectos
            INNER JOIN clientes ON proyectos.client_id = clientes.client_id
            LEFT JOIN proyecto_cantidad_servicios ON proyecto_cantidad_servicios.proy_id = proyectos.proy_id
            LEFT JOIN proyecto_gestionado ON proyecto_gestionado.id_proyecto_cantidad_servicios = proyecto_cantidad_servicios.id
            LEFT JOIN tm_categoria ON proyecto_gestionado.cat_id = tm_categoria.cat_id
            LEFT JOIN sectores ON sectores.sector_id = proyecto_gestionado.sector_id
            LEFT JOIN dimensionamiento ON proyecto_gestionado.id = dimensionamiento.id_proyecto_gestionado
            LEFT JOIN usuario_proyecto ON proyecto_gestionado.id = usuario_proyecto.id_proyecto_gestionado
            LEFT JOIN tm_usuario ON tm_usuario.usu_id = usuario_proyecto.usu_asignado
            LEFT JOIN pm_calidad ON pm_calidad.id_proyecto_gestionado = proyecto_gestionado.id
            LEFT JOIN tm_usuario AS tm_usuario_pm ON tm_usuario_pm.usu_id = pm_calidad.usu_crea
            LEFT JOIN (
            SELECT 
                tc.id_proyecto_gestionado,
                tc.usu_id,
                tc.id_pm_calidad,
                tm_usuario.usu_nom,
                SUM(TIME_TO_SEC(tc.horas_consumidas)) AS total_segundos
            FROM timesummary_carga tc
            INNER JOIN tm_usuario ON tm_usuario.usu_id = tc.usu_id
            GROUP BY tc.id_proyecto_gestionado, tc.usu_id, tc.id_pm_calidad, tm_usuario.usu_nom
        ) AS horas_usuarios ON horas_usuarios.id_proyecto_gestionado = proyecto_gestionado.id
        LEFT JOIN tm_estados ON proyecto_gestionado.estados_id = tm_estados.estados_id
        WHERE clientes.client_id=:client_id AND proyecto_gestionado.fech_inicio BETWEEN :fecha_desde AND :fecha_hasta AND proyecto_gestionado.estados_id IN(1,2,3,4,14)
        GROUP BY 
            clientes.client_id,
            clientes.client_rs,
            tm_categoria.cat_nom,
            sectores.sector_nombre,
            proyecto_gestionado.fech_inicio,
            proyecto_gestionado.fech_fin,
            dimensionamiento.hs_dimensionadas,
            tm_usuario_pm.usu_nom
        ORDER BY clientes.client_rs";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":client_id", $client_id, PDO::PARAM_STR);
        $stmt->bindValue(":fecha_desde", $fecha_desde, PDO::PARAM_STR);
        $stmt->bindValue(":fecha_hasta", $fecha_hasta, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDatosParaEventDrop($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM timesummary_carga WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}
