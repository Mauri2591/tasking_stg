<?php
class ProyectosVulmaGestion extends ConexionVulmaGestion
{
    public function proyectos_eh()
    {
        $conn = parent::get_conexion_vulma_gestion();
        $sql = "SELECT 
        tm_categoria.cat_nom AS producto,
        DATE_FORMAT(tm_ticket.fech_crea, '%Y-%m') AS mes,
        COUNT(*) AS total 
        FROM tm_ticket 
        INNER JOIN tm_categoria ON tm_categoria.cat_id = tm_ticket.cat_id 
        WHERE tm_ticket.estados_id IN (1,2,3,4)
        AND tm_ticket.fech_crea BETWEEN '2023-01-01' AND '2023-12-31 23:59:59' 
        AND tm_ticket.est = 1 
        GROUP BY tm_categoria.cat_id, tm_categoria.cat_nom, DATE_FORMAT(tm_ticket.fech_crea, '%Y-%m')
        ORDER BY producto, mes DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($datos) > 0 ? $datos : [];
    }
}
