<?php
class Activos extends Conexion
{

    public function getActivosPorSector(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tipo_activo.tipo AS tipo_activo FROM tipos_activos_sectores 
        INNER JOIN tipo_activo ON tipo_activo.id=tipos_activos_sectores.tipo_activo_id 
        INNER JOIN proyecto_gestionado ON proyecto_gestionado.sector_id=tipos_activos_sectores.sector_id 
        WHERE proyecto_gestionado.id=:id AND tipos_activos_sectores.est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($data) > 0 ? $data : [];
    }
    public function getHostsPorProyecto(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tipo, host FROM hosts WHERE id_proyecto_gestionado=:id AND est=1 ORDER BY host_id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $agrupados = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $agrupados[$row['tipo']][] = $row['host'];
        }
        return $agrupados;
    }
}
