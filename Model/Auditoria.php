<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Auditoria extends Conexion
{
    public function get_audit_sesiones()
    {
        $conn = parent::get_conexion();
        $conn->exec("SET time_zone = '-03:00'");
        $sql = "SELECT 
                l.id,
                CASE 
                    WHEN l.login = 'SI' THEN 'LOGIN'
                    WHEN l.logout = 'SI' THEN 'LOGOUT'
                END AS evento,
                DATE_FORMAT(l.fecha, '%d-%m-%Y %H:%i:%s') AS fecha,
                u.usu_correo,
                u.est,
                IF(u.est = 1, 'ACTIVO', 'INACTIVO') AS estado_usuario,
                s.sector_nombre
            FROM audit_login l
            INNER JOIN tm_usuario u ON u.usu_id = l.usu_id
            INNER JOIN sectores s ON s.sector_id = l.sector_id
            ORDER BY u.est DESC, l.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_audit_sesiones_x_fecha($desde, $hasta)
    {
        $conn = parent::get_conexion();
        $conn->exec("SET time_zone = '-03:00'");
        $sql = "SELECT 
        l.id,
        CASE 
            WHEN l.login  = 'SI' THEN 'LOGIN'
            WHEN l.logout = 'SI' THEN 'LOGOUT'
            ELSE 'DESCONOCIDO'
        END AS evento,
        DATE_FORMAT(l.fecha, '%d-%m-%Y %H:%i:%s') AS fecha,
        u.usu_correo,
        IF(u.est = 1, 'ACTIVO', 'INACTIVO') AS estado_usuario,
        s.sector_nombre
    FROM audit_login l
    INNER JOIN tm_usuario u ON u.usu_id = l.usu_id
    INNER JOIN sectores s ON s.sector_id = l.sector_id
    WHERE l.fecha >= :desde 
    AND l.fecha <  :hasta 
    ORDER BY u.est DESC, l.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":desde", $desde, PDO::PARAM_STR);
        $stmt->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_audit_estados_proyecto(string $id_proyecto_gestionado, int $estados_id, int $usu_id, int $sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO audit_estados_proyecto (id_proyecto_gestionado,estados_id,usu_id,sector_id) VALUES (:id_proyecto_gestionado,:estados_id,:usu_id,:sector_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(":estados_id", $estados_id, PDO::PARAM_INT);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function get_auditoria_proyectos()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT audit_estados_proyecto.id AS id_audit_estados_proyecto, 
        audit_estados_proyecto.id_proyecto_gestionado, 
        DATE_FORMAT(audit_estados_proyecto.fecha, '%d-%m-%Y %H:%i:%s') AS fecha, 
        tm_usuario.usu_correo, tm_usuario.est, sectores.sector_nombre, 
        tm_estados.estados_nombre AS evento, tm_estados.catColor AS color_estado, 
        tm_estados.icono AS icono,
        proyecto_gestionado.titulo, proyecto_gestionado.refProy FROM audit_estados_proyecto 
        INNER JOIN tm_usuario ON tm_usuario.usu_id=audit_estados_proyecto.usu_id 
        INNER JOIN sectores ON sectores.sector_id=audit_estados_proyecto.sector_id 
        INNER JOIN tm_estados ON tm_estados.estados_id=audit_estados_proyecto.estados_id
        LEFT JOIN proyecto_gestionado ON proyecto_gestionado.id=audit_estados_proyecto.id_proyecto_gestionado
        ORDER BY audit_estados_proyecto.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_auditoria_proyectos_x_fecha($desde, $hasta)
    {
        $conn = parent::get_conexion();
        $conn->exec("SET time_zone = '-03:00'");
        $sql = "SELECT 
        audit_estados_proyecto.id AS id_audit_estados_proyecto, 
        audit_estados_proyecto.id_proyecto_gestionado, 
        DATE_FORMAT(audit_estados_proyecto.fecha, '%d-%m-%Y %H:%i:%s') AS fecha, 
        tm_usuario.usu_correo, 
        tm_usuario.est,
        sectores.sector_nombre, 
        tm_estados.estados_nombre AS evento, 
        tm_estados.catColor AS color_estado, 
        tm_estados.icono AS icono,
        proyecto_gestionado.titulo, 
        proyecto_gestionado.refProy 
    FROM audit_estados_proyecto 
    INNER JOIN tm_usuario ON tm_usuario.usu_id = audit_estados_proyecto.usu_id 
    INNER JOIN sectores ON sectores.sector_id = audit_estados_proyecto.sector_id 
    INNER JOIN tm_estados ON tm_estados.estados_id = audit_estados_proyecto.estados_id
    LEFT JOIN proyecto_gestionado ON proyecto_gestionado.id = audit_estados_proyecto.id_proyecto_gestionado
    WHERE audit_estados_proyecto.fecha >= :desde 
    AND audit_estados_proyecto.fecha < :hasta
    ORDER BY audit_estados_proyecto.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":desde", $desde, PDO::PARAM_STR);
        $stmt->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_auditoria_proyectos_x_id($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT audit_estados_proyecto.id AS id_audit_estados_proyecto, 
            audit_estados_proyecto.id_proyecto_gestionado, 
            DATE_FORMAT(audit_estados_proyecto.fecha, '%d-%m-%Y %H:%i:%s') AS fecha, 
            tm_usuario.usu_correo, tm_usuario.est, sectores.sector_nombre, 
            tm_estados.estados_nombre AS evento, tm_estados.catColor AS color_estado, 
            tm_estados.icono AS icono,
            proyecto_gestionado.titulo, proyecto_gestionado.refProy FROM audit_estados_proyecto 
            INNER JOIN tm_usuario ON tm_usuario.usu_id=audit_estados_proyecto.usu_id 
            INNER JOIN sectores ON sectores.sector_id=audit_estados_proyecto.sector_id 
            INNER JOIN tm_estados ON tm_estados.estados_id=audit_estados_proyecto.estados_id
            LEFT JOIN proyecto_gestionado ON proyecto_gestionado.id=audit_estados_proyecto.id_proyecto_gestionado
            WHERE proyecto_gestionado.id = :id
            ORDER BY audit_estados_proyecto.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
