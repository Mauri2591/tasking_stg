<?php
class GestionActivos extends Conexion
{
    public function get_activos(int $sector_id)
    {
        $conn = parent::get_conexion();
        if ($_SESSION['sector_id'] == 4) {
            $sql = "SELECT id, gestion_activos.sector_id, host, ambiente, calidad, 
            DATE_FORMAT(alta, '%d-%m-%Y') AS alta, 
            gestion_activos.usu_crea, tm_usuario.usu_correo, sectores.sector_nombre AS sector FROM gestion_activos 
            INNER JOIN tm_usuario ON tm_usuario.usu_id=gestion_activos.usu_crea INNER JOIN sectores ON sectores.sector_id=gestion_activos.sector_id
            WHERE calidad='SI' AND gestion_activos.est=1 
            ORDER BY ambiente ASC";
        } else {
            $sql = "SELECT id, gestion_activos.sector_id, host, ambiente, calidad, 
            DATE_FORMAT(alta, '%d-%m-%Y') AS alta, 
            gestion_activos.usu_crea, tm_usuario.usu_correo 
            FROM gestion_activos 
            INNER JOIN tm_usuario ON tm_usuario.usu_id=gestion_activos.usu_crea 
            WHERE gestion_activos.sector_id=:sector_id AND gestion_activos.est=1 
            ORDER BY ambiente ASC";
        }
        $stmt = $conn->prepare($sql);
        if ($_SESSION['sector_id'] != 4) {
            $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($data) > 0 ? $data : [];
    }
}