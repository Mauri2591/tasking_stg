<?php
class Proyectos extends Conexion
{
    public function get_paises()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM tm_pais";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function crear_proyecto(int $usu_crea, int $client_id, int $pais_id, string $recurrencia, string $refPro, string $fechaVantive)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyectos (usu_crea,client_id,pais_id,recurrencia,refPro,fechaVantive,est) VALUES (?,?,?,?,?,?,1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($usu_crea, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($client_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(3, htmlspecialchars($pais_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(4, htmlspecialchars($recurrencia, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(5, htmlspecialchars($refPro, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(6, htmlspecialchars($fechaVantive, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function get_client_y_pais_para_proy_borrador($proy_id)
    {
        $conn = parent::get_conexion();
        //         $sql = "SELECT 
        //     c.client_rs, 
        //     DATE_FORMAT(p.fech_crea, '%d-%m-%Y') AS fech_crea,
        //     tp.pais_nombre,
        //     pg.id AS id_proyecto_gestionado,
        //     COUNT(pr.id) AS recurrencias_total,
        //     COUNT(CASE WHEN pr.est = 0 THEN 1 END) AS recurrencias_inactivas
        // FROM proyecto_gestionado pg
        // JOIN proyecto_cantidad_servicios pcs ON pg.id_proyecto_cantidad_servicios = pcs.id
        // JOIN proyectos p ON pcs.proy_id = p.proy_id
        // JOIN clientes c ON p.client_id = c.client_id
        // JOIN tm_pais tp ON c.pais_id = tp.pais_id
        // LEFT JOIN proyecto_recurrencia pr ON pr.id_proyecto_gestionado = pg.id
        // WHERE pg.id = ?
        // GROUP BY c.client_rs, p.fech_crea, tp.pais_nombre, pg.id";
        $sql = "SELECT clientes.client_rs, tm_pais.pais_nombre, DATE_FORMAT(fech_crea,'%d-%m-%Y') AS fech_crea FROM proyectos LEFT JOIN clientes ON proyectos.client_id=clientes.client_id LEFT JOIN tm_pais ON clientes.pais_id=tm_pais.pais_id WHERE proy_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($proy_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_proyecto_gestionado_para_cambiar_a_abier($proy_id, $id_proyecto_cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT proyecto_gestionado.id, proyecto_gestionado.proy_id, 
        proyecto_gestionado.estados_id, proyecto_gestionado.id_proyecto_cantidad_servicios, 
        proyecto_gestionado.recurrencia, proyecto_gestionado.estados_id FROM proyecto_gestionado 
        WHERE proy_id=? AND proyecto_gestionado.id_proyecto_cantidad_servicios=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($proy_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($id_proyecto_cantidad_servicios, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_estado_proy($id, $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_gestionado SET estados_id=:estados_id WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':estados_id', $estados_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function eliminar_proy_x_id($proy_id, $id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_cantidad_servicios SET est=0 WHERE proy_id=? AND id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($proy_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function cambiar_estado_proyecto_cantidad_servicios($id_proyecto_cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_cantidad_servicios SET est=0 WHERE id=:id_proyecto_cantidad_servicios";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function cambiar_a_eliminado_proyecto_gestionado($id, $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_gestionado SET estados_id= :estados_id WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":estados_id", $estados_id, PDO::PARAM_INT);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cambiar_a_abierto($id, $usu_crea)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_gestionado 
            SET usu_crea = :usu_crea, estados_id = 1 
            WHERE id = :id AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":usu_crea", $usu_crea, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }


    public function eliminar_proy_x_numero_servicio_proy_gestionado($id)
    {
        $conn = parent::get_conexion();
        $sql = "DELETE FROM proyecto_gestionado WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert_proyecto(int $usu_id, int $client_id, int $cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyectos (usu_crea, client_id, cantidad_servicios, est) VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $client_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $cantidad_servicios, PDO::PARAM_INT);
        $stmt->execute();
        // Devuelvo el ID generado
        return $conn->lastInsertId();
    }
    public function insert_proyectos_tasking(int $id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyectos_tasking (
            id_proyecto_gestionado,
            fech_habilitacion
        ) VALUES (
            :id_proyecto_gestionado,
            DATE_ADD(CURDATE(), INTERVAL 2 MONTH)
        )";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function update_proyecto_desarrollo_tasking($id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyectos_tasking 
            SET finalizado = 'SI' WHERE id_proyecto_gestionado = :id_proyecto_gestionado";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function inhabilitar_proyectos_desarrollo_tasking($id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyectos_tasking SET est=0 WHERE id_proyecto_gestionado = :id_proyecto_gestionado";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function get_datos_proyectos_tasking()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT fech_crea, fech_habilitacion,finalizado,est FROM proyectos_tasking WHERE est=1 ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function proyecto_cantidad_servicios(int $proy_id, int $usu_id, $numero_servicio)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyecto_cantidad_servicios (proy_id, usu_crea,numero_servicio) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $proy_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $numero_servicio, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function insert_host(int $id_proyecto_gestionado, int $id_proyecto_cantidad_servicios, int $usu_crea, string $tipo, string $valor)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO hosts (id_proyecto_gestionado, id_proyecto_cantidad_servicios, usu_crea, tipo, host, fecha_carga, est) VALUES (?, ?, ?, ?, ?, NOW(), 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(2, $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->bindValue(3, $usu_crea, PDO::PARAM_INT);
        $stmt->bindValue(4, $tipo);
        $stmt->bindValue(5, $valor);
        $stmt->execute();
    }

    public function insert_nuevos_host(int $id_proyecto_gestionado, $id_proyecto_cantidad_servicios, int $usu_id, string $tipo, string $host)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO hosts (id_proyecto_gestionado,id_proyecto_cantidad_servicios,usu_crea, tipo, host, fecha_carga, est) VALUES (?, ?, ?, ?, ?, NOW(), 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $id_proyecto_gestionado);
        $stmt->bindValue(2, $id_proyecto_cantidad_servicios);
        $stmt->bindValue(3, $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(4, $tipo);
        $stmt->bindValue(5, $host);
        $stmt->execute();
    }

    public function get_proyectos_nuevos_borrador()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio, 
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,           -- ahora viene del proyecto_gestionado
    s.sector_nombre,
    tc.cat_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pr.posicion_recurrencia,
    prioridad.prioridad,
    IF(proyecto_rechequeo.id,'SI','NO') as rechequeo,
    CASE 
        WHEN pg.id_proyecto_recurrencia IS NULL THEN 0 
        ELSE pg.id_proyecto_recurrencia 
    END AS id_proyecto_recurrencia,
    GROUP_CONCAT(tmu.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id           -- ✅ cambio aquí
LEFT JOIN tm_categoria tc 
    ON pg.cat_id = tc.cat_id
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN usuario_proyecto AS ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario tmu 
    ON ua.usu_asignado = tmu.usu_id
LEFT JOIN proyecto_recurrencia pr 
    ON pg.id_proyecto_recurrencia = pr.id
LEFT JOIN prioridad 
    ON pg.prioridad_id = prioridad.id
LEFT JOIN proyecto_rechequeo 
    ON pg.id = proyecto_rechequeo.id_proyecto_gestionado
WHERE pcs.est = 1 
  AND (pg.estados_id IS NULL OR pg.estados_id = 14)
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pcs.fech_crea, 
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    tc.cat_nom,
    tp.pais_nombre,
    pg.id,
    pr.posicion_recurrencia,
    pg.id_proyecto_recurrencia
ORDER BY pcs.proy_id ASC, pcs.numero_servicio ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_proyecto_recurrencia($id, $id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_recurrencia SET activo='SI',id_proyecto_gestionado=:id_proyecto_gestionado WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function update_id_proyecto_recurrencia_proyecto_gestionado($id, $id_proyecto_recurrencia)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_gestionado SET id_proyecto_recurrencia=:id_proyecto_recurrencia WHERE id=:id AND estados_id=14 AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":id_proyecto_recurrencia", $id_proyecto_recurrencia, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function get_proyectos_nuevos_x_sector($sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
        pcs.id AS id_proyecto_cantidad_servicios,
        pcs.proy_id, 
        pcs.numero_servicio, 
        DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
        DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
        p.cantidad_servicios, 
        c.client_rs, 
        u.usu_nom AS creador_proy,
        s.sector_nombre,
        s.sector_id,
        tc.cat_nom,
        tp.pais_nombre,
        pg.id,
        d.hs_dimensionadas,
        ua.usu_asignado
        FROM proyecto_cantidad_servicios pcs
        JOIN proyectos p ON pcs.proy_id = p.proy_id
        LEFT JOIN clientes c ON p.client_id = c.client_id
        LEFT JOIN tm_pais tp ON c.pais_id = tp.pais_id
        LEFT JOIN tm_usuario u ON p.usu_crea = u.usu_id
        LEFT JOIN proyecto_gestionado pg ON pg.id_proyecto_cantidad_servicios = pcs.id
        LEFT JOIN tm_usuario ug ON pg.usu_crea = ug.usu_id
        LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
        LEFT JOIN sectores s ON pg.sector_id = s.sector_id
        LEFT JOIN dimensionamiento d ON d.id_proyecto_gestionado = pg.id -- ✅ JOIN sin filtro restrictivo
        LEFT JOIN usuario_proyecto AS ua ON pg.id=ua.id_proyecto_gestionado
        WHERE s.sector_id = ? 
        AND pcs.est = 1 
        AND (pg.estados_id IS NULL OR pg.estados_id IN (1))
        ORDER BY pcs.proy_id ASC, pcs.numero_servicio ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Inician servicios ETHICAL HACKING
    //VAS

    public function get_proyectos_eh($sector_id, $cat_id, $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,              -- ✅ ahora del proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    pr.posicion_recurrencia,
    CASE 
        WHEN pg.id_proyecto_recurrencia IS NULL THEN 0 
        ELSE pg.id_proyecto_recurrencia 
    END AS id_proyecto_recurrencia,
    IF(proyecto_rechequeo.id IS NOT NULL, 'SI', 'NO') AS rechequeo,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    GROUP_CONCAT(uas.usu_id SEPARATOR ',') AS usu_id_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom AS categoria

FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id             -- ✅ cambio clave
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
LEFT JOIN proyecto_recurrencia pr 
    ON pg.id_proyecto_recurrencia = pr.id
LEFT JOIN proyecto_rechequeo 
    ON pg.id = proyecto_rechequeo.id_proyecto_gestionado

WHERE 
    s.sector_id = ? 
    AND pcs.est = 1 
    AND pg.cat_id = ? 
    AND pg.estados_id = ?

GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom,
    pr.posicion_recurrencia,
    pg.id_proyecto_recurrencia,
    proyecto_rechequeo.id

ORDER BY 
    id_proyecto_cantidad_servicios ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($cat_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(3, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // FINALIZAN servicios ETHICAL HACKING VAS

    // Inician INCIDENT RESPONSE
    public function get_proyectos_incident_response($sector_id, $estados_id, $cat_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,              -- ✅ ahora viene del proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    pr.posicion_recurrencia,
    IF(proyecto_rechequeo.id, 'SI', 'NO') AS rechequeo,
    CASE 
        WHEN pg.id_proyecto_recurrencia IS NULL THEN 0 
        ELSE pg.id_proyecto_recurrencia 
    END AS id_proyecto_recurrencia,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    GROUP_CONCAT(uas.usu_id SEPARATOR ',') AS usu_id_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom AS categoria
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id             -- ✅ cambio aquí
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto AS ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
LEFT JOIN proyecto_recurrencia pr 
    ON pg.id_proyecto_recurrencia = pr.id
LEFT JOIN proyecto_rechequeo 
    ON pg.id = proyecto_rechequeo.id_proyecto_gestionado
WHERE pcs.est = 1 
  AND pg.estados_id = :estados_id
  AND (
        s.sector_id = :sector_id
        OR pg.cat_id = :cat_id
      )
  AND pg.cat_id = :cat_id
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom,
    pr.posicion_recurrencia,
    pg.id_proyecto_recurrencia,
    rechequeo
ORDER BY id_proyecto_cantidad_servicios ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        $stmt->bindValue(':estados_id', $estados_id, PDO::PARAM_INT);
        $stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //Finaliza INCIDENT RESPONSE


    // INICIAN servicios SOC
    public function get_proyectos_sase($sector_id, $cat_id, $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,            -- ✅ ahora desde proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    pr.posicion_recurrencia,
    IF(proyecto_rechequeo.id, 'SI', 'NO') AS rechequeo,
    CASE 
        WHEN pg.id_proyecto_recurrencia IS NULL THEN 0 
        ELSE pg.id_proyecto_recurrencia 
    END AS id_proyecto_recurrencia,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom AS categoria
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id              -- ✅ cambio clave
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
LEFT JOIN proyecto_recurrencia pr 
    ON pg.id_proyecto_recurrencia = pr.id
LEFT JOIN proyecto_rechequeo 
    ON pg.id = proyecto_rechequeo.id_proyecto_gestionado
WHERE s.sector_id = ?
  AND pcs.est = 1 
  AND pg.cat_id = ?
  AND pg.estados_id = ?
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom,
    pr.posicion_recurrencia,
    rechequeo,
    pg.id_proyecto_recurrencia
ORDER BY id_proyecto_cantidad_servicios ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($cat_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(3, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //FINALIZA servicios SASE

    // INICIAN servicios SOC
    public function get_proyectos_soc($sector_id, $cat_id, $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,              -- ✅ ahora el creador del proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    pr.posicion_recurrencia,
    IF(proyecto_rechequeo.id, 'SI', 'NO') AS rechequeo,
    CASE 
        WHEN pg.id_proyecto_recurrencia IS NULL THEN 0 
        ELSE pg.id_proyecto_recurrencia 
    END AS id_proyecto_recurrencia,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom AS categoria
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id              -- ✅ cambio clave
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
LEFT JOIN proyecto_recurrencia pr 
    ON pg.id_proyecto_recurrencia = pr.id          
LEFT JOIN proyecto_rechequeo 
    ON pg.id = proyecto_rechequeo.id_proyecto_gestionado 
WHERE s.sector_id = ?
  AND pcs.est = 1 
  AND pg.cat_id = ?
  AND pg.estados_id = ?
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom,
    pr.posicion_recurrencia,
    rechequeo,
    pg.id_proyecto_recurrencia
ORDER BY id_proyecto_cantidad_servicios ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($cat_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(3, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //FINALIZAN servicios SOC

    public function get_usuarios_x_proy_y_sector(int $id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    tm_usuario.usu_nom,
    usuario_proyecto.usu_asignado,
    sectores.sector_nombre,
    sectores.sector_id,
    proyecto_gestionado.estados_id
FROM proyecto_gestionado
LEFT JOIN usuario_proyecto 
    ON usuario_proyecto.id_proyecto_gestionado = proyecto_gestionado.id
LEFT JOIN tm_usuario 
    ON usuario_proyecto.usu_asignado = tm_usuario.usu_id
LEFT JOIN sectores 
    ON tm_usuario.sector_id = sectores.sector_id
WHERE proyecto_gestionado.id = :id_proyecto_gestionado";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_si_proy_recurrencia_is_null($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT IF(id_proyecto_recurrencia IS NULL,'NULO','NO_NULO') AS id_proyecto_recurrencia FROM proyecto_gestionado WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_descripciones_proyecto_get_id(int $id_proyecto_cantidad_servicios, int $id_proyecto_gestionado, int $usu_crea, string $descripcion_proyecto, string $captura_imagen): array
    {
        $conn = parent::get_conexion();
        $carpeta_hash = uniqid(md5(rand()), true); // Hash único

        $sql = "INSERT INTO descripciones_proyecto 
        (id_proyecto_cantidad_servicios,id_proyecto_gestionado, usu_crea, descripcion_proyecto, captura_imagen, carpeta_documentos_proy)
        VALUES (:id_proy,:id_proyecto_gestionado,  :usu, :desc, :captura, :carpeta)";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proy', $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(':usu', $usu_crea, PDO::PARAM_INT);
        $stmt->bindValue(':desc', $descripcion_proyecto, PDO::PARAM_STR);
        $stmt->bindValue(':captura', $captura_imagen, PDO::PARAM_STR);
        $stmt->bindValue(':carpeta', $carpeta_hash, PDO::PARAM_STR);
        $stmt->execute();

        return [
            "id" => $conn->lastInsertId(),
            "hash" => $carpeta_hash
        ];
    }

    public function asociar_documentos_a_descripcion(int $id, string $documentos)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE descripciones_proyecto SET documento = :documentos WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':documentos', $documentos, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function finalizar_proyecto(int $estados_id, int $id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_gestionado SET estados_id=:estados_id WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':estados_id', $estados_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function finalizar_proyecto_tabla_estados_proyecto(int $id_proyecto_gestionado, int $usu_id, int $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO estados_proyecto(id_proyecto_gestionado,usu_id,estados_id) VALUES (:id_proyecto_gestionado, :usu_id, :estados_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(':estados_id', $estados_id, PDO::PARAM_INT);
        $stmt->bindValue(':usu_id', $usu_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function get_datos_usuario_finalizador_proyecto(int $id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
        tm_usuario.usu_nom, 
        sectores.sector_nombre,
        estados_proyecto.fecha_cierre_proyecto
        FROM estados_proyecto 
        LEFT JOIN tm_usuario ON estados_proyecto.usu_id = tm_usuario.usu_id 
        LEFT JOIN sectores ON tm_usuario.sector_id = sectores.sector_id 
        WHERE estados_proyecto.id_proyecto_gestionado = :id_proyecto_gestionado ORDER BY estados_proyecto.fecha_cierre_proyecto DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_descripciones_proyecto(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT descripciones_proyecto.id,descripciones_proyecto.carpeta_documentos_proy,descripciones_proyecto.documento,descripciones_proyecto.descripcion_proyecto,descripciones_proyecto.captura_imagen,descripciones_proyecto.usu_crea, descripciones_proyecto.fech_crea, tm_usuario.usu_nom, sectores.sector_nombre, proyecto_gestionado.estados_id FROM descripciones_proyecto LEFT JOIN tm_usuario ON descripciones_proyecto.usu_crea=tm_usuario.usu_id LEFT JOIN sectores ON tm_usuario.sector_id=sectores.sector_id LEFT JOIN proyecto_gestionado ON descripciones_proyecto.id_proyecto_gestionado=proyecto_gestionado.id WHERE proyecto_gestionado.id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete_descripciones_proyecto(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "DELETE FROM descripciones_proyecto WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function get_proyectos_nuevos_vista_calidad($sector_id, $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,            -- ✅ ahora desde proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id              -- ✅ cambio clave
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
WHERE 
    s.sector_id = ?
    AND pcs.est = 1 
    AND pg.estados_id = ?
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom
ORDER BY id_proyecto_cantidad_servicios ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_proyectos_en_proceso_vista_calidad()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,                 -- ✅ ahora desde proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    proyecto_recurrencia.posicion_recurrencia,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    CASE 
        WHEN proyecto_rechequeo.id_proyecto_gestionado IS NOT NULL THEN 'SI'
        ELSE 'NO'
    END AS rechequeo,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id                -- ✅ creador del proyecto gestionado
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
LEFT JOIN proyecto_rechequeo 
    ON proyecto_rechequeo.id_proyecto_gestionado = pg.id
LEFT JOIN proyecto_recurrencia 
    ON pg.id = proyecto_recurrencia.id_proyecto_gestionado
WHERE 
    pcs.est = 1 
    AND (pg.estados_id = 1 OR pg.estados_id = 2)
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom
ORDER BY id_proyecto_cantidad_servicios ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_proyectos_($estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,               -- ✅ ahora desde proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom AS categoria
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id                -- ✅ cambio clave
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
WHERE 
    pcs.est = 1 
    AND pg.estados_id = ?
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom
ORDER BY 
    id_proyecto_cantidad_servicios ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_proyectos_realizados_vista_calidad($estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pcs.id AS id_proyecto_cantidad_servicios,
    pcs.proy_id, 
    pcs.numero_servicio, 
    DATE_FORMAT(pg.fech_inicio, '%d-%m-%Y') AS fech_inicio,
    DATE_FORMAT(pg.fech_fin, '%d-%m-%Y') AS fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom AS creador_proy,               -- ✅ ahora desde proyecto_gestionado
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id AS prioridad,
    prio.prioridad AS prioridad_nom,
    GROUP_CONCAT(uas.usu_nom SEPARATOR ',<br>') AS usu_nom_asignado,
    (
        SELECT SUM(d.hs_dimensionadas)
        FROM dimensionamiento d
        WHERE d.id_proyecto_gestionado = pg.id
    ) AS hs_dimensionadas,
    tmc.cat_nom AS categoria
FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
LEFT JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN tm_pais tp 
    ON c.pais_id = tp.pais_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
LEFT JOIN tm_usuario u 
    ON pg.usu_crea = u.usu_id                -- ✅ cambio clave
LEFT JOIN sectores s 
    ON pg.sector_id = s.sector_id
LEFT JOIN tm_subcategoria tsc 
    ON pg.cats_id = tsc.cats_id
LEFT JOIN tm_categoria tmc 
    ON pg.cat_id = tmc.cat_id
LEFT JOIN prioridad prio 
    ON pg.prioridad_id = prio.id
LEFT JOIN usuario_proyecto ua 
    ON pg.id = ua.id_proyecto_gestionado
LEFT JOIN tm_usuario uas 
    ON ua.usu_asignado = uas.usu_id
WHERE 
    pcs.est = 1 
    AND pg.estados_id = ?
GROUP BY 
    pcs.id,
    pcs.proy_id, 
    pcs.numero_servicio, 
    pg.fech_inicio,
    pg.fech_fin,
    p.cantidad_servicios, 
    c.client_rs, 
    u.usu_nom,
    s.sector_nombre,
    s.sector_id,
    tsc.cats_nom,
    tp.pais_nombre,
    pg.id,
    pg.cat_id,
    pg.estados_id,
    pg.titulo,
    prio.id,
    prio.prioridad,
    tmc.cat_nom
ORDER BY id_proyecto_cantidad_servicios ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_proyecto_gestionado(int $id_proyecto_cantidad_servicios, int $cat_id, int $cats_id, int $sector_id, int $usu_crea, string $prioridad_id, int $estados_id, string $titulo, string $descripcion, string $refProy, string $recurrencia, string $fech_inicio, string $fech_fin, string $fech_vantive, $archivo, $captura_imagen)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyecto_gestionado (
            id_proyecto_cantidad_servicios,
            cat_id,
            cats_id,
            sector_id,
            usu_crea,
            prioridad_id,
            estados_id,
            titulo,
            descripcion,
            refProy,
            recurrencia,
            fech_inicio,
            fech_fin,
            fech_vantive,
            archivo,
            captura_imagen,
            est
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($id_proyecto_cantidad_servicios, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($cat_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(3, htmlspecialchars($cats_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(4, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        // $stmt->bindValue(5, htmlspecialchars($usu_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(5, htmlspecialchars($usu_crea, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(6, htmlspecialchars($prioridad_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(7, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(8, htmlspecialchars($titulo, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(9, htmlspecialchars($descripcion, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(10, htmlspecialchars($refProy, ENT_QUOTES), PDO::PARAM_STR); // corregido
        $stmt->bindValue(11, htmlspecialchars($recurrencia, ENT_QUOTES), PDO::PARAM_STR);

        $stmt->bindValue(12, $fech_inicio, is_null($fech_inicio) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(13, $fech_fin, is_null($fech_fin) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(14, $fech_vantive, is_null($fech_vantive) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $stmt->bindValue(15, $archivo, PDO::PARAM_STR);
        $stmt->bindValue(16, $captura_imagen, PDO::PARAM_STR);
        $stmt->bindValue(17, 1, PDO::PARAM_INT); // est activo por defecto
        $stmt->execute();
        return $conn->lastInsertId();
    }

    public function insert_proyecto_pm(int $id_proyecto_gestionado, $horas_pm, $usu_crea, $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO pm_calidad (id_proyecto_gestionado,horas_pm,usu_crea,estados_id) VALUES (:id_proyecto_gestionado,:horas_pm,:usu_crea,:estados_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(':horas_pm', $horas_pm, PDO::PARAM_STR_CHAR);
        $stmt->bindValue(':usu_crea', $usu_crea, PDO::PARAM_INT);
        $stmt->bindValue(':estados_id', $estados_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insert_proyecto_recurrencia(int $id_proyecto_cantidad_servicios, int $cat_id)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyecto_recurrencia (id_proyecto_cantidad_servicios,cat_id) VALUES (:id_proyecto_cantidad_servicios,:cat_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proyecto_cantidad_servicios', $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insert_usuarios_proyecto(int $id_proyecto_gestionado, $usu_asignado)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO usuario_proyecto (id_proyecto_gestionado, usu_asignado)
            VALUES (:id_proyecto_gestionado, :usu_asignado)";
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);

        // 👇 Bloque nuevo
        if (is_null($usu_asignado) || $usu_asignado === '') {
            $stmt->bindValue(':usu_asignado', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':usu_asignado', $usu_asignado, PDO::PARAM_INT);
        }

        $stmt->execute();
    }

    public function insertar_usuarios_a_recurrente(int $id_proyecto_gestionado, $usu_asignado)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO usuario_proyecto (id_proyecto_gestionado, usu_asignado)
            VALUES (:id_proyecto_gestionado, :usu_asignado)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(':usu_asignado', $usu_asignado);
        $stmt->execute();
    }


    public function insert_dimensionamiento(int $id_proyecto_gestionado, string $hs_dimensionadas, int $usu_crea)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO dimensionamiento (id_proyecto_gestionado, hs_dimensionadas, usu_crea) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($id_proyecto_gestionado, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($hs_dimensionadas, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(3, htmlspecialchars($usu_crea, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function get_datos_proyecto_creado(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pg.*, 
    d.hs_dimensionadas,
    pr.posicion_recurrencia
FROM 
    proyecto_gestionado pg
LEFT JOIN 
    dimensionamiento d 
        ON pg.id = d.id_proyecto_gestionado
LEFT JOIN 
    proyecto_recurrencia pr
        ON pg.id = pr.id 
WHERE 
    pg.id = ?
    AND pg.est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function finalizar_proyecto_sin_implementar_proyecto_gestionado(int $id_proyecto_gestionado, int $estados_id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_gestionado SET estados_id=?, est=0 WHERE id_proyecto_gestionado=? AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($estados_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($id_proyecto_gestionado, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function update_proyecto(
        int $id,
        int $cat_id,
        int $cats_id,
        int $sector_id,
        int $usu_id,
        int $usu_crea,
        int $prioridad_id,
        string $titulo,
        string $descripcion,
        string $refProy,
        string $recurrencia,
        string $fech_inicio,
        string $fech_fin,
        string $fech_vantive
    ) {
        try {
            $conn = parent::get_conexion();
            $sql = "UPDATE proyecto_gestionado 
                    SET cat_id = :cat_id,
                        cats_id = :cats_id,
                        sector_id = :sector_id,
                        usu_crea = :usu_crea,
                        prioridad_id = :prioridad_id,
                        titulo = :titulo,
                        descripcion = :descripcion,
                        refProy = :refProy,
                        recurrencia = :recurrencia,
                        fech_inicio = :fech_inicio,
                        fech_fin = :fech_fin,
                        fech_vantive = :fech_vantive
                    WHERE id = :id
                      AND est = 1";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
            $stmt->bindValue(':cats_id', $cats_id, PDO::PARAM_INT);
            $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
            $stmt->bindValue(':usu_crea', $usu_crea, PDO::PARAM_INT);
            $stmt->bindValue(':prioridad_id', $prioridad_id, PDO::PARAM_INT);
            $stmt->bindValue(':titulo', htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'), PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'), PDO::PARAM_STR);
            $stmt->bindValue(':refProy', htmlspecialchars($refProy, ENT_QUOTES, 'UTF-8'), PDO::PARAM_STR);
            $stmt->bindValue(':recurrencia', htmlspecialchars($recurrencia, ENT_QUOTES, 'UTF-8'), PDO::PARAM_STR);
            $stmt->bindValue(':fech_inicio', $fech_inicio, is_null($fech_inicio) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':fech_fin', $fech_fin, is_null($fech_fin) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':fech_vantive', $fech_vantive, is_null($fech_vantive) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount(); // cantidad de filas actualizadas
        } catch (PDOException $e) {
            // Loguear el error si querés (por ejemplo usando error_log())
            error_log("Error en update_proyecto: " . $e->getMessage());
            return false; // podés devolver false si algo falla
        }
    }

    public function update_usuarios_asignados(int $id_proyecto_gestionado, array $usuarios_ids)
    {
        try {
            $conn = parent::get_conexion();

            // Eliminar todos los usuarios asignados actualmente
            $sqlDelete = "DELETE FROM usuario_proyecto WHERE id_proyecto_gestionado = :id";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bindValue(':id', $id_proyecto_gestionado, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Insertar los nuevos usuarios asignados (si hay)
            if (!empty($usuarios_ids)) {
                $sqlInsert = "INSERT INTO usuario_proyecto (id_proyecto_gestionado, usu_asignado) VALUES (:id, :usu_id)";
                $stmtInsert = $conn->prepare($sqlInsert);
                foreach ($usuarios_ids as $usu_id) {
                    $stmtInsert->bindValue(':id', $id_proyecto_gestionado, PDO::PARAM_INT);
                    $stmtInsert->bindValue(':usu_id', $usu_id, PDO::PARAM_INT);
                    $stmtInsert->execute();
                }
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error en update_usuarios_asignados: " . $e->getMessage());
            return false;
        }
    }



    public function update_hs_dimensionadas(int $id_proyecto_gestionado, string $hs_dimensionadas, int $usu_crea)
    {
        try {
            $conn = parent::get_conexion();

            error_log("Entrando a update_hs_dimensionadas()");
            error_log("ID: $id_proyecto_gestionado, HS: $hs_dimensionadas, USU: $usu_crea");

            $sql = "UPDATE dimensionamiento 
                    SET hs_dimensionadas = :hs_dimensionadas, usu_crea = :usu_crea 
                    WHERE id_proyecto_gestionado = :id_proyecto_gestionado";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':hs_dimensionadas', htmlspecialchars($hs_dimensionadas, ENT_QUOTES, 'UTF-8'), PDO::PARAM_STR);
            $stmt->bindValue(':usu_crea', $usu_crea, PDO::PARAM_INT);
            $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->rowCount();
            error_log("✅ Filas afectadas por update_hs_dimensionadas: $rows");

            return $rows;
        } catch (PDOException $e) {
            error_log("ERROR en update_hs_dimensionadas: " . $e->getMessage());
            return false;
        }
    }

    public function finalizar_proyecto_sin_implementar_proyecto_cantidad_servicios(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_cantidad_servicios SET est=0 WHERE id=? AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }
    public function get_combo_categorias_x_sector(int $sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT cat_id, cat_nom FROM tm_categoria WHERE sector_id = ? AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_combo_subcategorias_x_sector(int $sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM tm_subcategoria WHERE sector_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sectores()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT sector_id, sector_nombre FROM sectores WHERE est=1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_combo_prioridad_proy_nuevo_eh()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM prioridad";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_datos_proyecto_x_proy_id($proy_id, $id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT clientes.client_rs, proyectos.refPro, proyectos.recurrencia 
        AS recurrencia_original, proyectos_contador_recurrencia.recurrencia, 
        tm_usuario.usu_nom, sectores.sector_nombre FROM proyectos 
        LEFT JOIN clientes ON proyectos.client_id=clientes.client_id 
        LEFT JOIN proyectos_contador_recurrencia 
        ON proyectos.proy_id=proyectos_contador_recurrencia.proy_id 
        LEFT JOIN tm_usuario ON proyectos.usu_crea=tm_usuario.usu_id 
        LEFT JOIN sectores ON tm_usuario.sector_id=sectores.sector_id 
        WHERE proyectos.proy_id=? 
        AND proyectos_contador_recurrencia.id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($proy_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function get_primer_recurrencia($proy_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT recurrencia FROM proyectos_contador_recurrencia WHERE proy_id=? ORDER BY id ASC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($proy_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_data_proyecto_cantidad_servicios($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM proyecto_cantidad_servicios WHERE id=? AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // public function get_nom_sector_x_id_recurrente($id)
    // {
    //     $conn = parent::get_conexion();
    //     $sql = "SELECT sectores.sector_nombre FROM proyectos_contador_recurrencia 
    //     INNER JOIN sectores ON proyectos_contador_recurrencia.sector_id=sectores.sector_id 
    //     WHERE id=?";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bindValue(1, htmlspecialchars($id, ENT_QUOTES), PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public function inactivar_host_x_id($usu_crea, $id_proyecto_gestionado, $host_id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE hosts SET est=0, usu_crea=? WHERE id_proyecto_gestionado=? AND host_id=? AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($usu_crea, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($id_proyecto_gestionado, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(3, htmlspecialchars($host_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function inactivar_todos_los_host_x_id_proyecto_cantidad_servicios($usu_crea, $id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE hosts SET est=0, usu_crea=? WHERE id_proyecto_gestionado=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($usu_crea, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($id_proyecto_gestionado, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function activar_host_x_id($usu_crea, $id_proyecto_gestionado, $host_id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE hosts SET est=1, usu_crea=? WHERE id_proyecto_gestionado=? AND host_id=? AND est=0";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($usu_crea, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(2, htmlspecialchars($id_proyecto_gestionado, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->bindValue(3, htmlspecialchars($host_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }


    public function get_host_proy_borrador($id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM hosts WHERE id_proyecto_gestionado=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($id_proyecto_gestionado, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_hosts_proy($id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tipo,host FROM hosts WHERE id_proyecto_gestionado=:id_proyecto_gestionado AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create_proyecto_a_nuevo(int $proy_id, int $sector_id, int $usu_crea, int $contador_id, int $prioridad_id, $documento, string $fecha_ini, string $fecha_fin)
    {
        $conn = parent::get_conexion();
        if ($documento == null) {
            $sql = "INSERT INTO proyecto_iniciado (proy_id, sector_id, usu_crea, contador_id, prioridad_id, documento, fecha_ini, fecha_fin, est)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(1, $proy_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $sector_id, PDO::PARAM_INT);
            $stmt->bindValue(3, $usu_crea, PDO::PARAM_INT);
            $stmt->bindValue(4, $contador_id, PDO::PARAM_INT);
            $stmt->bindValue(5, $prioridad_id, PDO::PARAM_INT);
            $stmt->bindValue(6, null); // Guardás solo la ruta relativa
            $stmt->bindValue(7, $fecha_ini);
            $stmt->bindValue(8, $fecha_fin);
            $stmt->execute();
        } else {
            $hash_doc = md5(uniqid(rand(), true));
            $extension = strtolower(pathinfo($documento['name'], PATHINFO_EXTENSION));
            $nombreArchivo = $hash_doc . '.' . $extension;

            $carpeta = __DIR__ . "/../../View/Home/Public/uploads";
            $rutaRelativa = $nombreArchivo;
            $rutaCompleta = $carpeta . '/' . $nombreArchivo;

            // Crear carpeta si no existe
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            // Guardar archivo
            if (move_uploaded_file($documento['tmp_name'], $rutaCompleta)) {
                $sql = "INSERT INTO proyecto_iniciado (proy_id, sector_id, usu_crea, contador_id, prioridad_id, documento, fecha_ini, fecha_fin, est)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $proy_id, PDO::PARAM_INT);
                $stmt->bindValue(2, $sector_id, PDO::PARAM_INT);
                $stmt->bindValue(3, $usu_crea, PDO::PARAM_INT);
                $stmt->bindValue(4, $contador_id, PDO::PARAM_INT);
                $stmt->bindValue(5, $prioridad_id, PDO::PARAM_INT);
                $stmt->bindValue(6, $rutaRelativa); // Guardás solo la ruta relativa
                $stmt->bindValue(7, $fecha_ini);
                $stmt->bindValue(8, $fecha_fin);
                $stmt->execute();
            } else {
                throw new Exception("No se pudo guardar el archivo");
            }
        }
    }

    public function get_sector_x_proy($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT sector_id FROM proyecto_gestionado WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_usuarios_x_sector($sector_id)
    {
        $conn = parent::get_conexion();
        if ($sector_id == 5) {
            $sql = "SELECT tm_usuario.usu_id, tm_usuario.usu_correo, tm_usuario.usu_nom, sectores.sector_nombre FROM tm_usuario LEFT JOIN sectores ON tm_usuario.sector_id=sectores.sector_id WHERE sectores.sector_nombre IS NOT NULL AND tm_usuario.usu_id != 82 AND tm_usuario.est=1";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT tm_usuario.usu_id, tm_usuario.usu_correo, tm_usuario.usu_nom, sectores.sector_nombre FROM tm_usuario LEFT JOIN sectores ON tm_usuario.sector_id=sectores.sector_id WHERE tm_usuario.sector_id=? AND sectores.sector_nombre IS NOT NULL AND tm_usuario.usu_id != 82 AND tm_usuario.est=1 AND tm_usuario.est=1";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(1, htmlspecialchars($sector_id, ENT_QUOTES), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function delete_proyecto_a_nuevo($proy_id)
    {
        $conn = parent::get_conexion();
        $sql = "DELETE FROM proyectos WHERE proy_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($proy_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete_proyecto_a_nuevo_proyectos_contador_recurrencia($proy_id)
    {
        $conn = parent::get_conexion();
        $sql = "DELETE FROM proyectos_contador_recurrencia WHERE proy_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($proy_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
    }

    function tomar_proyecto($usu_id, $id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO usuario_proyecto (id_proyecto_gestionado, usu_asignado) 
            VALUES (:id_proyecto_gestionado, :usu_asignado)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id_proyecto_gestionado', $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(':usu_asignado', $usu_id, PDO::PARAM_INT);
        $stmt->execute();
    }


    public function get_datos_proyecto_gestionado($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT proyecto_gestionado.*, proyecto_recurrencia.posicion_recurrencia, 
        tm_categoria.cat_nom, tm_subcategoria.cats_nom, if(proyecto_rechequeo.id, 'SI','NO') AS rechequeo, 
        if(workshop.est = 1,'SI','NO') AS workshop FROM proyecto_gestionado 
        LEFT JOIN tm_categoria ON proyecto_gestionado.cat_id = tm_categoria.cat_id 
        LEFT JOIN tm_subcategoria ON proyecto_gestionado.cats_id = tm_subcategoria.cats_id 
        LEFT JOIN proyecto_rechequeo ON proyecto_rechequeo.id_proyecto_gestionado=proyecto_gestionado.id 
        LEFT JOIN proyecto_recurrencia ON proyecto_gestionado.id=proyecto_recurrencia.id_proyecto_gestionado 
        LEFT JOIN workshop ON proyecto_gestionado.id=workshop.id_proyecto_gestionado 
        WHERE proyecto_gestionado.id = :id AND proyecto_gestionado.est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    public function grafico_get_total_servicios($sector_id)
    {
        $conn = parent::get_conexion();
        if ($sector_id == 4) {
            // $sql = "SELECT cat_nom, COUNT(*) AS total FROM ( 
            // -- Proyectos actuales 
            // SELECT tc.cat_nom FROM proyecto_gestionado pg LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id WHERE pg.estados_id = 4 AND tc.est = 1 UNION ALL 
            // -- Proyectos antiguos 
            // SELECT tc.cat_nom FROM tm_ticket t LEFT JOIN tm_categoria tc ON t.cat_id = tc.cat_id WHERE t.estados_id = 4 AND tc.est = 1 ) AS sub GROUP BY cat_nom;";

            // $sql = "SELECT COUNT(proyecto_gestionado.id) AS total, tm_categoria.cat_nom 
            // FROM proyecto_gestionado INNER JOIN tm_categoria ON proyecto_gestionado.cat_id=tm_categoria.cat_id 
            // WHERE proyecto_gestionado.estados_id=4 
            // GROUP BY proyecto_gestionado.cat_id";
            $sql = "SELECT 
                tm_categoria.cat_nom,
                COUNT(proyecto_gestionado.id) AS total
            FROM proyecto_gestionado
            INNER JOIN tm_categoria ON proyecto_gestionado.cat_id = tm_categoria.cat_id
            WHERE proyecto_gestionado.estados_id = 4
            GROUP BY tm_categoria.cat_nom
            ORDER BY total DESC";
            $stmt = $conn->prepare($sql);
        } else {
            //             $sql = "SELECT 
            //     cat_nom,
            //     COUNT(*) AS total
            // FROM (
            //     -- Proyectos actuales
            //     SELECT 
            //         tc.cat_nom
            //     FROM proyecto_gestionado pg
            //     LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
            //     WHERE pg.estados_id = 4 
            //       AND (tc.sector_id = :sector_id OR tc.cat_id = 26)
            //     UNION ALL
            //     -- Proyectos antiguos
            //     SELECT 
            //         tc.cat_nom
            //     FROM tm_ticket t
            //     LEFT JOIN tm_categoria tc ON t.cat_id = tc.cat_id
            //     WHERE t.estados_id = 4 
            //       AND (tc.sector_id = :sector_id OR tc.cat_id = 26)
            // ) AS sub
            // GROUP BY cat_nom";
            $sql = "SELECT COUNT(proyecto_gestionado.id) AS total, tm_categoria.cat_nom 
            FROM proyecto_gestionado 
            INNER JOIN tm_categoria ON proyecto_gestionado.cat_id=tm_categoria.cat_id 
            WHERE proyecto_gestionado.sector_id=:sector_id AND proyecto_gestionado.estados_id=4 
            GROUP BY proyecto_gestionado.cat_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function grafico_get_total_servicios_por_sector()
    {
        $conn = parent::get_conexion();
        //         $sql = "SELECT 
        //     sector_nombre,
        //     COUNT(*) AS total
        // FROM (
        //     -- Proyectos actuales
        //     SELECT 
        //         s.sector_nombre
        //     FROM proyecto_gestionado pg
        //     LEFT JOIN sectores s ON pg.sector_id = s.sector_id
        //     WHERE pg.estados_id = 4
        //     UNION ALL
        //     -- Proyectos antiguos
        //     SELECT 
        //         s.sector_nombre
        //     FROM tm_ticket t
        //     LEFT JOIN sectores s ON t.sector = s.sector_id
        //     WHERE t.estados_id = 4
        // ) AS sub
        // GROUP BY sector_nombre";
        $sql = "SELECT COUNT(proyecto_gestionado.id) AS total, sectores.sector_nombre AS sector_nombre FROM proyecto_gestionado INNER JOIN sectores ON proyecto_gestionado.sector_id=sectores.sector_id WHERE proyecto_gestionado.estados_id=4 GROUP BY sector_nombre";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_proyectos_total()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    c.client_id,
    c.client_rs,
    COUNT(DISTINCT pg.id) AS cantidad_proyectos
FROM clientes c
LEFT JOIN proyectos p 
    ON p.client_id = c.client_id
LEFT JOIN proyecto_cantidad_servicios pcs 
    ON pcs.proy_id = p.proy_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
WHERE pcs.est = 1 
GROUP BY c.client_id, c.client_rs
ORDER BY cantidad_proyectos DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_proyectos_total_excel_x_sector($sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    c.client_id, 
    c.client_rs, 
    COUNT(DISTINCT pg.id) AS cantidad_proyectos
    FROM clientes c
    LEFT JOIN proyectos p ON p.client_id = c.client_id
    LEFT JOIN proyecto_cantidad_servicios pcs ON pcs.proy_id = p.proy_id
    LEFT JOIN proyecto_gestionado pg ON pg.id_proyecto_cantidad_servicios = pcs.id
    LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
    LEFT JOIN sectores s ON pg.sector_id = s.sector_id
    WHERE (s.sector_id = :sector_id OR tc.cat_id = 26) 
    AND pcs.est = 1
    GROUP BY c.client_id, c.client_rs
    ORDER BY cantidad_proyectos DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function get_proyectos_total_excel($fecha_desde = null, $fecha_hasta = null)
    {
        $conn = parent::get_conexion();
        $sector_id = $_SESSION['sector_id'] ?? null;

        $sql = "SELECT 
                c.client_id,
                c.client_rs,
                tc.cat_nom AS categoria,
                COUNT(DISTINCT pg.id) AS cantidad_proyectos
            FROM clientes c
            LEFT JOIN proyectos p ON p.client_id = c.client_id
            LEFT JOIN proyecto_cantidad_servicios pcs ON pcs.proy_id = p.proy_id
            LEFT JOIN proyecto_gestionado pg ON pg.id_proyecto_cantidad_servicios = pcs.id
            LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
            WHERE pcs.est = 1";
        if (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $sql .= " AND pg.fech_inicio BETWEEN :fecha_desde AND :fecha_hasta";
        }
        if (!empty($sector_id) && $sector_id != 4) {
            $sql .= " AND pg.sector_id = :sector_id";
        }
        $sql .= " GROUP BY c.client_id, c.client_rs, tc.cat_nom
              ORDER BY c.client_rs, tc.cat_nom";
        $stmt = $conn->prepare($sql);
        if (!empty($fecha_desde) && !empty($fecha_hasta)) {
            $stmt->bindValue(":fecha_desde", $fecha_desde, PDO::PARAM_STR);
            $stmt->bindValue(":fecha_hasta", $fecha_hasta, PDO::PARAM_STR);
        }
        if (!empty($sector_id) && $sector_id != 4) {
            $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_proyectos_total_x_client_id($client_id, $sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT  
            pg.id,
            pg.titulo,
            pg.fech_vantive,
            pg.fech_crea,
            pg.refProy AS referencia,
            pg.fech_fin,
            pg.fech_inicio,
            s.sector_nombre,
            c.cat_nom AS producto,
            cl.client_rs AS cliente,
            tm_estados.estados_nombre AS estado,
            pcs.id AS id_proyecto_cantidad_servicios,
            prc.posicion_recurrencia,
            IF(pr.id IS NULL, NULL, 'SI') AS rechequeo,
            pr.id_proyecto_gestionado_origen AS rechequeo_de,  
            SUM(DISTINCT d.hs_dimensionadas) AS dimensionamiento,
            SUM(CASE WHEN hosts.tipo = 'IP' THEN 1 ELSE 0 END) AS ips,
            SUM(CASE WHEN hosts.tipo = 'URL' THEN 1 ELSE 0 END) AS urls,
            SUM(CASE WHEN hosts.tipo NOT IN ('IP','URL') THEN 1 ELSE 0 END) AS otros
        FROM proyecto_gestionado pg
        LEFT JOIN sectores s ON pg.sector_id = s.sector_id
        LEFT JOIN tm_categoria c ON pg.cat_id = c.cat_id
        LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado
        LEFT JOIN proyecto_cantidad_servicios pcs ON pg.id_proyecto_cantidad_servicios = pcs.id
        LEFT JOIN proyectos p ON pcs.proy_id = p.proy_id
        LEFT JOIN proyecto_recurrencia prc ON pg.id = prc.id_proyecto_gestionado
        LEFT JOIN clientes cl ON p.client_id = cl.client_id
        LEFT JOIN tm_estados ON pg.estados_id = tm_estados.estados_id
        LEFT JOIN proyecto_rechequeo pr ON pg.id = pr.id_proyecto_gestionado
        LEFT JOIN hosts ON pg.id = hosts.id_proyecto_gestionado
        WHERE cl.client_id = :client_id
        AND (
            (:sector_id = 4) OR (pg.cat_id IN (
                SELECT cat_id FROM tm_categoria WHERE sector_id = :sector_id OR cat_id = 26
            ))
        )
        GROUP BY pg.id, pg.titulo, pg.fech_vantive, s.sector_nombre, c.cat_nom, cl.client_rs, pg.refProy
        ORDER BY pg.refProy, pg.id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":client_id", $client_id, PDO::PARAM_INT);
        $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDatosCliente($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT proyecto_gestionado.id, proyecto_gestionado.refProy, clientes.client_rs 
        FROM proyecto_gestionado LEFT JOIN proyecto_cantidad_servicios 
        ON proyecto_gestionado.id_proyecto_cantidad_servicios = proyecto_cantidad_servicios.id 
        LEFT JOIN proyectos ON proyecto_cantidad_servicios.proy_id = proyectos.proy_id 
        LEFT JOIN clientes ON proyectos.client_id = clientes.client_id 
        WHERE proyecto_gestionado.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function get_sectores_x_sector_id($sector_id)
    {
        $conn = parent::get_conexion();

        // 🔹 Si no es sector 4, filtra por sector y categoría 26 (permiso especial)
        $join_pg_sector = $sector_id != 4
            ? "AND (pg.sector_id = :sector_id_pg OR tc.cat_id = 26)"
            : "";

        $where_tc_sector = $sector_id != 4
            ? "AND (tc.sector_id = :sector_id OR tc.cat_id = 26)"
            : "";

        // 🔹 Incluimos los estados 1 y 2 en la condición del JOIN
        $sql = "SELECT 
                tc.cat_id,
                tc.cat_nom,
                tc.nombre_ruta,
                s.sector_id,
                COUNT(pg.id) AS total
            FROM tm_categoria tc
            LEFT JOIN sectores s 
                ON tc.sector_id = s.sector_id
            LEFT JOIN proyecto_gestionado pg 
                ON pg.cat_id = tc.cat_id 
                AND pg.estados_id IN (1, 2)  -- ✅ Ahora incluye estado 1 o 2
                $join_pg_sector
            WHERE tc.est = 1 
                $where_tc_sector
            GROUP BY 
                tc.cat_id, tc.cat_nom, s.sector_id
            ORDER BY 
                CASE WHEN COUNT(pg.id) > 0 THEN 0 ELSE 1 END,
                COUNT(pg.id) DESC";

        $stmt = $conn->prepare($sql);

        if ($sector_id != 4) {
            $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
            $stmt->bindValue(':sector_id_pg', $sector_id, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_proyectos_nuevos_x_sector_inicio($sector_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT COUNT(proyecto_gestionado.id) AS total, tm_categoria.cat_nom FROM proyecto_gestionado LEFT JOIN tm_categoria ON proyecto_gestionado.cat_id = tm_categoria.cat_id WHERE proyecto_gestionado.estados_id = 1 AND proyecto_gestionado.sector_id = :sector_id GROUP BY tm_categoria.cat_nom";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignar_fecha_proyecto_finalizado_sin_fecha_fin($id, $fech_fin)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_gestionado SET fech_fin=:fech_fin WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':fech_fin', htmlspecialchars($fech_fin, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->execute();

        // Agregamos respuesta
        echo json_encode([
            "Status" => $stmt->rowCount() > 0 ? "OK" : "ERROR",
            "rowsAffected" => $stmt->rowCount()
        ]);
    }

    public function get_proyectos_recurrentes()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    MAX(pg.titulo) AS titulo,
    tc.cat_nom, 
    c.client_rs, 
    MAX(pg.id) AS id_proyecto_gestionado,  
    pcs.id AS id_proyecto_cantidad_servicios,

    -- Total de recurrencias
    (
        SELECT COUNT(*)
        FROM proyecto_recurrencia prx
        WHERE prx.id_proyecto_cantidad_servicios = pcs.id
    ) AS recurrencias_total,

    -- Utilizadas (activo = 'SI')
    (
        SELECT COUNT(*)
        FROM proyecto_recurrencia prx
        WHERE prx.id_proyecto_cantidad_servicios = pcs.id
          AND prx.activo = 'SI'
    ) AS recurrencias_utilizadas,

    -- Restantes (total - utilizadas)
    (
        SELECT COUNT(*)
        FROM proyecto_recurrencia prx
        WHERE prx.id_proyecto_cantidad_servicios = pcs.id
    ) -
    (
        SELECT COUNT(*)
        FROM proyecto_recurrencia prx
        WHERE prx.id_proyecto_cantidad_servicios = pcs.id
          AND prx.activo = 'SI'
    ) AS recurrencias_restantes,

    -- Primer ID pendiente
    (
        SELECT MIN(pr2.id)
        FROM proyecto_recurrencia pr2
        WHERE pr2.id_proyecto_cantidad_servicios = pcs.id
          AND pr2.activo = 'NO'
    ) AS conteo_id_recurrencia,

    -- Estado de toma
    CASE 
        WHEN SUM(CASE WHEN pg.id_proyecto_recurrencia IS NULL THEN 1 ELSE 0 END) > 0 
            THEN 'NO_TOMADO'
        ELSE 'SI_TOMADO'
    END AS estado_recurrencia

FROM proyecto_cantidad_servicios pcs
JOIN proyectos p 
    ON pcs.proy_id = p.proy_id
JOIN clientes c 
    ON p.client_id = c.client_id
LEFT JOIN proyecto_gestionado pg 
    ON pg.id_proyecto_cantidad_servicios = pcs.id
JOIN tm_categoria tc 
    ON pg.cat_id = tc.cat_id

-- 🔹 Solo proyectos y servicios activos
WHERE pcs.est = 1 
  AND (pg.est = 1 OR pg.est IS NULL)

GROUP BY tc.cat_nom, c.client_rs, pcs.id
HAVING recurrencias_total > recurrencias_utilizadas
ORDER BY pcs.id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_id_proyecto_recurrencia($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT id_proyecto_recurrencia FROM proyecto_gestionado WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function get_datos_ver_recurrente($id_proyecto_cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pg.titulo AS TITULO, 
    pg.refProy AS REFERENCIA, 
    sectores.sector_nombre AS SECTOR,
    tm_categoria.cat_nom AS CATEGORIA, 
    tm_subcategoria.cats_nom AS SUBCATEGORIA, 
    CONCAT(d.hs_dimensionadas, 'hs') AS DIMENSIONAMIENTO, 
    prioridad.prioridad AS PRIORIDAD,
    creador.usu_correo AS CREADOR, 
    pg.fech_vantive AS FECHA_VANTIVE, 
    pg.fech_crea AS FECHA_CREACION,
    CASE 
        WHEN pg.archivo IS NOT NULL AND pg.archivo <> '' 
        THEN 'POSEE' 
        ELSE 'NO POSEE' 
    END AS ARCHIVO,
    CASE 
        WHEN pg.captura_imagen IS NOT NULL AND pg.captura_imagen <> '' 
        THEN 'POSEE' 
        ELSE 'NO POSEE' 
    END AS CAPTURA_IMAGEN
FROM proyecto_gestionado pg
LEFT JOIN tm_categoria 
    ON pg.cat_id = tm_categoria.cat_id
LEFT JOIN tm_subcategoria 
    ON pg.cats_id = tm_subcategoria.cats_id
LEFT JOIN sectores 
    ON pg.sector_id = sectores.sector_id

-- Join a usuario creador
LEFT JOIN tm_usuario AS creador 
    ON pg.usu_crea = creador.usu_id

-- Join a tabla de asignaciones
LEFT JOIN usuario_proyecto 
    ON usuario_proyecto.id_proyecto_gestionado = pg.id

-- Join a usuarios asignados
LEFT JOIN tm_usuario AS asignado 
    ON usuario_proyecto.usu_asignado = asignado.usu_id

LEFT JOIN prioridad 
    ON pg.prioridad_id = prioridad.id

-- Subquery para el primer id de recurrencia
LEFT JOIN (
    SELECT 
        pr.id_proyecto_cantidad_servicios,
        MIN(pr.id) AS primer_recurrencia_id
    FROM proyecto_recurrencia pr
    GROUP BY pr.id_proyecto_cantidad_servicios
) pr_first
    ON pr_first.id_proyecto_cantidad_servicios = pg.id_proyecto_cantidad_servicios

-- Dimensionamiento ligado al primer id de recurrencia
LEFT JOIN dimensionamiento d
    ON d.id_proyecto_gestionado = pr_first.primer_recurrencia_id

LEFT JOIN hosts 
    ON pg.id = hosts.id_proyecto_gestionado

WHERE pg.id_proyecto_cantidad_servicios = :id_proyecto_cantidad_servicios

GROUP BY 
    pg.id, 
    tm_categoria.cat_nom, 
    tm_subcategoria.cats_nom, 
    sectores.sector_nombre, 
    creador.usu_correo, 
    prioridad.prioridad, 
    d.hs_dimensionadas, 
    pg.titulo, 
    pg.descripcion, 
    pg.refProy, 
    pg.fech_vantive, 
    pg.archivo, 
    pg.captura_imagen, 
    pg.fech_crea";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Con esto traigo el primer id de proyecto_recurrente para actualizar activo='SI' de proyecto_recurrente
    public function get_primer_id_proyecto_recurrencia($id_proyecto_cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT MIN(id) AS id, activo FROM proyecto_recurrencia WHERE id_proyecto_cantidad_servicios =:id_proyecto_cantidad_servicios AND activo='NO' AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function validar_si_es_recurrente($id_proyecto_cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
            IF(
                EXISTS(
                    SELECT 1 
                    FROM proyecto_recurrencia 
                    WHERE id_proyecto_cantidad_servicios = :id_proyecto_cantidad_servicios 
                ),
                'SI_RECURRENTE',
                'NO_RECURRENTE'
            ) AS validacion_recurrente";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    //Con esto traigo el primer id de proyecto_gestionado para actualizar el id_proyecto_recurrente de proyecto_gestionado
    public function get_primer_id_proyecto_gestionado($id_proyecto_cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT MIN(id) AS id_proyecto_gestionado FROM proyecto_gestionado WHERE id_proyecto_cantidad_servicios=:id_proyecto_cantidad_servicios AND estados_id=14 AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function get_datos_recurrente_para_insert($id_proyecto_cantidad_servicios)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT 
    pg.id AS id_proyecto_gestionado,
    pg.cat_id,
    pg.cats_id,
    pg.id_proyecto_cantidad_servicios,
    pg.sector_id,
    pg.usu_crea,
    pg.prioridad_id,
    pg.estados_id,
    pg.titulo,
    pg.descripcion,
    pg.refProy,
    pg.recurrencia,
    pg.fech_vantive,
    pg.archivo,
    pg.captura_imagen,
    pg.fech_crea,
    pg.est,
    d.hs_dimensionadas,
    pr_first.primer_recurrencia_id
FROM proyecto_gestionado pg
LEFT JOIN (
    SELECT 
        pr.id_proyecto_cantidad_servicios,
        MIN(pr.id) AS primer_recurrencia_id
    FROM proyecto_recurrencia pr
    GROUP BY pr.id_proyecto_cantidad_servicios
) pr_first
    ON pr_first.id_proyecto_cantidad_servicios = pg.id_proyecto_cantidad_servicios
LEFT JOIN dimensionamiento d
    ON d.id_proyecto_gestionado = pg.id   -- dimensionamiento se relaciona con proyecto_gestionado
WHERE pg.id_proyecto_cantidad_servicios = :id_proyecto_cantidad_servicios";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_recurrente_proy_gestionado(
        $id_proyecto_cantidad_servicios,
        $cat_id,
        $cats_id,
        $sector_id,
        $usu_crea,
        $prioridad_id,
        $estados_id,
        $titulo,
        $descripcion,
        $refProy,
        $recurrencia,
        $fech_vantive,
        $archivo,
        $captura_imagen,
        $fech_crea,
        $est
    ) {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyecto_gestionado (
                id_proyecto_cantidad_servicios,
                cat_id,
                cats_id,
                sector_id,
                usu_crea,
                prioridad_id,
                estados_id,
                titulo,
                descripcion,
                refProy,
                recurrencia,
                fech_vantive,
                archivo,
                captura_imagen,
                fech_crea,
                est
            ) VALUES (
                :id_proyecto_cantidad_servicios,
                :cat_id,
                :cats_id,
                :sector_id,
                :usu_crea,
                :prioridad_id,
                :estados_id,
                :titulo,
                :descripcion,
                :refProy,
                :recurrencia,
                :fech_vantive,
                :archivo,
                :captura_imagen,
                :fech_crea,
                :est)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->bindValue(":cat_id", $cat_id, PDO::PARAM_INT);
        $stmt->bindValue(":cats_id", $cats_id, PDO::PARAM_INT);
        $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        $stmt->bindValue(":usu_crea", $usu_crea, PDO::PARAM_INT);
        $stmt->bindValue(":prioridad_id", $prioridad_id, PDO::PARAM_INT);
        $stmt->bindValue(":estados_id", $estados_id, PDO::PARAM_INT);
        $stmt->bindValue(":titulo", $titulo, PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(":refProy", $refProy, PDO::PARAM_STR);
        $stmt->bindValue(":recurrencia", $recurrencia, PDO::PARAM_STR);
        $stmt->bindValue(":fech_vantive", $fech_vantive, PDO::PARAM_STR);
        $stmt->bindValue(":archivo", $archivo, PDO::PARAM_STR);
        $stmt->bindValue(":captura_imagen", $captura_imagen, PDO::PARAM_STR);
        $stmt->bindValue(":fech_crea", $fech_crea, PDO::PARAM_STR);
        $stmt->bindValue(":est", $est, PDO::PARAM_INT);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    // public function get_fecha_inicio_proy($id){
    //     $conn=parent::get_conexion();
    //     $sql="SELECT fech_inicio FROM gestionar_proy_borrador WHERE id=:id";
    //     $stmt=$conn->prepare($sql);
    //     $stmt->bindValue("id",$id,PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public function insert_dimensionamiento_recurrente_proy_gestionado($id_proyecto_gestionado, $hs_dimensionadas, $usu_crea)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO dimensionamiento (id_proyecto_gestionado,hs_dimensionadas,usu_crea, est) VALUES (:id_proyecto_gestionado,:hs_dimensionadas, :usu_crea,1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(":hs_dimensionadas", $hs_dimensionadas, PDO::PARAM_STR);
        $stmt->bindValue(":usu_crea", $usu_crea, PDO::PARAM_INT);
        $stmt->execute();
    }

    //**************************  RECHEQUEOS  ************************************* */

    public function get_datos_para_insert_rechequeo($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT proyecto_gestionado.*, proyecto_recurrencia.posicion_recurrencia, dimensionamiento.hs_dimensionadas as dimensionamiento FROM proyecto_gestionado LEFT JOIN dimensionamiento ON proyecto_gestionado.id=dimensionamiento.id_proyecto_gestionado LEFT JOIN proyecto_recurrencia ON proyecto_gestionado.id=proyecto_recurrencia.id_proyecto_gestionado WHERE proyecto_gestionado.id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_rechequeo(
        $id_proyecto_cantidad_servicios,
        $id_proyecto_recurrencia,
        $cat_id,
        $cats_id,
        $sector_id,
        $usu_crea,
        $prioridad_id,
        $estados_id,
        $titulo,
        $descripcion,
        $refProy,
        $fech_vantive,
        $archivo,
        $captura_imagen
    ) {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyecto_gestionado (
                id_proyecto_cantidad_servicios,
                id_proyecto_recurrencia,
                cat_id,
                cats_id,
                sector_id,
                usu_crea,
                prioridad_id,
                estados_id,
                titulo,
                descripcion,
                refProy,
                fech_vantive,
                archivo,
                captura_imagen
            ) VALUES (
                :id_proyecto_cantidad_servicios,
                :id_proyecto_recurrencia,
                :cat_id,
                :cats_id,
                :sector_id,
                :usu_crea,
                :prioridad_id,
                :estados_id,
                :titulo,
                :descripcion,
                :refProy,
                :fech_vantive,
                :archivo,
                :captura_imagen
            )";
        $stmt = $conn->prepare($sql);
        // 🔹 Bind de todos los valores
        $stmt->bindValue(":id_proyecto_cantidad_servicios", $id_proyecto_cantidad_servicios, PDO::PARAM_INT);
        $stmt->bindValue(":id_proyecto_recurrencia", $id_proyecto_recurrencia, PDO::PARAM_INT);
        $stmt->bindValue(":cat_id", $cat_id, PDO::PARAM_INT);
        $stmt->bindValue(":cats_id", $cats_id, PDO::PARAM_INT);
        $stmt->bindValue(":sector_id", $sector_id, PDO::PARAM_INT);
        $stmt->bindValue(":usu_crea", $usu_crea, PDO::PARAM_INT);
        $stmt->bindValue(":prioridad_id", $prioridad_id, PDO::PARAM_INT);
        $stmt->bindValue(":estados_id", $estados_id, PDO::PARAM_INT);
        $stmt->bindValue(":titulo", $titulo, PDO::PARAM_STR);
        $stmt->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(":refProy", $refProy, PDO::PARAM_STR);
        $stmt->bindValue(":fech_vantive", $fech_vantive, PDO::PARAM_STR);
        $stmt->bindValue(":archivo", $archivo, PDO::PARAM_STR);
        $stmt->bindValue(":captura_imagen", $captura_imagen, PDO::PARAM_STR);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    public function update_proyecto_rechequeo_posicion_recurrencia($posicion_recurrencia, $id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE proyecto_rechequeo SET id_proyecto_gestionado=:id_proyecto_gestionado, posicion_recurrencia=:posicion_recurrencia WHERE id_proyecto_gestionado=:id_proyecto_gestionado";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":posicion_recurrencia", $posicion_recurrencia, PDO::PARAM_STR);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insert_proyecto_rechequeo($id_proyecto_gestionado, $id_proyecto_gestionado_origen)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO proyecto_rechequeo (id_proyecto_gestionado,id_proyecto_gestionado_origen) VALUES (:id_proyecto_gestionado,:id_proyecto_gestionado_origen)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(":id_proyecto_gestionado_origen", $id_proyecto_gestionado_origen, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function get_workshop($id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM workshop WHERE id_proyecto_gestionado = :id_proyecto_gestionado LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_workshop($id_proyecto_gestionado)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO workshop (id_proyecto_gestionado) VALUES (:id_proyecto_gestionado)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function update_workshop($id_proyecto_gestionado, $est)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE workshop SET est=:est WHERE id_proyecto_gestionado=:id_proyecto_gestionado";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":est", $est, PDO::PARAM_INT);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insert_dimensionamiento_de_rechequeo($id_proyecto_gestionado, $hs_dimensionadas)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO dimensionamiento(id_proyecto_gestionado,hs_dimensionadas) VALUES (:id_proyecto_gestionado,:hs_dimensionadas)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_proyecto_gestionado", $id_proyecto_gestionado, PDO::PARAM_INT);
        $stmt->bindValue(":hs_dimensionadas", htmlentities($hs_dimensionadas, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->execute();
    }
}
