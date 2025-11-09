<?php

class Tymesummary extends Conexion
{

    public function get_tareas($usu_id = null)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
            t.id,
            t.fecha,
            t.hora_desde,
            t.hora_hasta,
            p.titulo AS titulo_proyecto,
            t.descripcion,
            t.id_proyecto_gestionado,
            t.id_producto,
            t.fecha as fecha,
            tareas.id as id_tarea,
            tareas.nombre as nombre_tarea,
            tm_categoria.cat_nom as producto
            FROM timesummary_carga t
            INNER JOIN proyecto_gestionado p 
                ON t.id_proyecto_gestionado = p.id
                LEFT JOIN tm_categoria ON t.id_producto=tm_categoria.cat_id
                LEFT JOIN tareas ON t.id_tarea=tareas.id
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
            if (empty($val)) {
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

    public function insert_tarea($usu_id, $id_proyecto_gestionado, $id_producto, $id_tarea, $fecha, $hora_desde, $hora_hasta, $descripcion, $horas_consumidas)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO timesummary_carga
        (usu_id, id_proyecto_gestionado, id_producto,id_tarea, fecha, hora_desde, hora_hasta, descripcion,horas_consumidas)
        VALUES (:usu_id, :id_proyecto_gestionado, :id_producto,:id_tarea, :fecha, :hora_desde, :hora_hasta, :descripcion,:horas_consumidas)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt->bindValue(":id_tarea", $id_tarea, PDO::PARAM_INT);
        $stmt->bindValue(":fecha", htmlentities($fecha, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":hora_desde", htmlentities($hora_desde, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":hora_hasta", htmlentities($hora_hasta, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", htmlentities($descripcion, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":horas_consumidas", $horas_consumidas, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function get_titulos_proyectos($usu_asignado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    tse.id AS id_timesummary_estados,
    pg.id AS id_proyecto_gestionado,
    pg.titulo,
    tm_categoria.cat_nom AS producto,

    -- 🔹 Total de horas dimensionadas
    IFNULL(dim.total_hs_dimensionadas, 0) AS hs_dimensionadas,

    -- 🔹 Horas consumidas por el usuario actual
    IFNULL(
        (
            SELECT 
                TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(tc2.horas_consumidas))), '%H:%i')
            FROM timesummary_carga tc2
            WHERE 
                tc2.id_proyecto_gestionado = pg.id
                AND tc2.usu_id = :usu_asignado
                AND tc2.est = 1
        ),
        '00:00'
    ) AS horas_consumidas,

    -- 🔹 Total de horas consumidas por todos los usuarios
    IFNULL(
        (
            SELECT 
                TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(tc3.horas_consumidas))), '%H:%i')
            FROM timesummary_carga tc3
            WHERE 
                tc3.id_proyecto_gestionado = pg.id
                AND tc3.est = 1
        ),
        '00:00'
    ) AS horas_total,

    -- 🔹 Comparación entre horas_total y horas_dimensionadas
    CASE 
        WHEN (
            -- Convertimos las horas totales a segundos
            (SELECT SUM(TIME_TO_SEC(tc4.horas_consumidas)) 
             FROM timesummary_carga tc4 
             WHERE tc4.id_proyecto_gestionado = pg.id AND tc4.est = 1)
        ) > (dim.total_hs_dimensionadas * 3600) 
        THEN 'HORAS_TOTAL_MAYOR_QUE_DIM'
        ELSE 'HORAS_TOTAL_MENOR_QUE_DIM'
    END AS comparacion_horas,

    tse.est

FROM proyecto_gestionado pg

LEFT JOIN tm_estados e 
    ON pg.estados_id = e.estados_id

LEFT JOIN tm_categoria 
    ON pg.cat_id = tm_categoria.cat_id

INNER JOIN timesummary_estados tse 
    ON pg.id = tse.id_proyecto_gestionado
    AND tse.usuario_asignado = :usu_asignado
    AND tse.est = 1

-- 🔹 Subconsulta para total dimensionadas
LEFT JOIN (
    SELECT 
        id_proyecto_gestionado,
        SUM(hs_dimensionadas) AS total_hs_dimensionadas
    FROM dimensionamiento
    GROUP BY id_proyecto_gestionado
) AS dim 
    ON dim.id_proyecto_gestionado = pg.id

WHERE 
    e.estados_id IN (1, 2, 3, 4)

GROUP BY 
    tse.id, pg.id, pg.titulo, tm_categoria.cat_nom, dim.total_hs_dimensionadas, tse.est

ORDER BY 
    pg.titulo";
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
            pg.id AS id_proyecto_gestionado,
            pg.titulo,
            tm_categoria.cat_nom AS producto,
            IFNULL(dim.total_hs_dimensionadas, 0) AS hs_dimensionadas,
            tse.est
        FROM proyecto_gestionado pg
        LEFT JOIN tm_estados e 
            ON pg.estados_id = e.estados_id
        LEFT JOIN tm_categoria 
            ON pg.cat_id = tm_categoria.cat_id
        INNER JOIN timesummary_estados tse 
            ON pg.id = tse.id_proyecto_gestionado
            AND tse.usuario_asignado = :usu_asignado 
        LEFT JOIN (
            SELECT 
                id_proyecto_gestionado,
                SUM(hs_dimensionadas) AS total_hs_dimensionadas
            FROM dimensionamiento
            GROUP BY id_proyecto_gestionado
        ) AS dim 
            ON dim.id_proyecto_gestionado = pg.id
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
}
