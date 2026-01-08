<?php
class Integraciones extends Conexion
{
    public function get_herramientas()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT id, herramienta FROM herramienas_integraciones WHERE est=1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $resul = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }

    public function get_api_keys()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT api_keys.id, api_key, herramienas_integraciones.herramienta AS herramienta, sectores.sector_nombre AS sector, tm_usuario.usu_nom AS usu_crea, IF(api_keys.est = 1, 'ACTIVO','INACTIVO') AS estado FROM api_keys INNER JOIN herramienas_integraciones ON api_keys.id_herramienta=herramienas_integraciones.id INNER JOIN sectores ON api_keys.sector_id=sectores.sector_id INNER JOIN tm_usuario ON api_keys.usu_crea=tm_usuario.usu_id WHERE api_keys.est=1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $resul = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }

    public function inhabilitar_api_keys($id)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE api_keys SET est=0 WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function existe_api_key_activa_x_sector($sector_id): bool
    {
        $conn = parent::get_conexion();

        $sql = "SELECT 1 
            FROM api_keys 
            WHERE sector_id = :sector_id 
              AND est = 1
            LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    public function crear_api_key($api_key_hash, $sector_id, $id_herramienta, $usu_crea)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO api_keys 
            (api_key, sector_id, id_herramienta, usu_crea) 
            VALUES 
            (:api_key, :sector_id, :id_herramienta, :usu_crea)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':api_key', $api_key_hash, PDO::PARAM_STR);
        $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        $stmt->bindValue(':id_herramienta', $id_herramienta, PDO::PARAM_INT);
        $stmt->bindValue(':usu_crea', $usu_crea, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
