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
                u.est AS estado_usuario,
                s.sector_nombre
            FROM audit_login l
            INNER JOIN tm_usuario u ON u.usu_id = l.usu_id
            INNER JOIN sectores s ON s.sector_id = l.sector_id
            ORDER BY u.est DESC, l.fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
