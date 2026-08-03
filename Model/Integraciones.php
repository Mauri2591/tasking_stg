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
        $sql = "SELECT api_keys.id, api_key, herramienas_integraciones.herramienta AS herramienta, sectores.sector_nombre AS sector, tm_usuario.usu_nom AS usu_crea FROM api_keys INNER JOIN herramienas_integraciones ON api_keys.id_herramienta=herramienas_integraciones.id INNER JOIN sectores ON api_keys.sector_id=sectores.sector_id INNER JOIN tm_usuario ON api_keys.usu_crea=tm_usuario.usu_id WHERE api_keys.est=1";
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

    public function insertar_resumen_documento_ia(
        int $id_descripciones_proyecto,
        string $documento,
        string $resumen,
        string $modelo_usado,
        int $usu_crea,
        string $tipo_prompt = 'default',
        ?string $prompt_usado = null
    ): bool {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO resumen_documentos_ia
            (id_descripciones_proyecto, documento, resumen, modelo_usado, tipo_prompt, prompt_usado, usu_crea, fech_crea)
        VALUES
            (:id_descripciones_proyecto, :documento, :resumen, :modelo_usado, :tipo_prompt, :prompt_usado, :usu_crea, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_descripciones_proyecto", $id_descripciones_proyecto, PDO::PARAM_INT);
        $stmt->bindValue(":documento", $documento, PDO::PARAM_STR);
        $stmt->bindValue(":resumen", $resumen, PDO::PARAM_STR);
        $stmt->bindValue(":modelo_usado", $modelo_usado, PDO::PARAM_STR);
        $stmt->bindValue(":tipo_prompt", $tipo_prompt, PDO::PARAM_STR);
        $stmt->bindValue(":prompt_usado", $prompt_usado, $prompt_usado === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(":usu_crea", $usu_crea, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtener_ids_con_resumen_ia(array $ids_descripciones): array
    {
        if (empty($ids_descripciones)) {
            return [];
        }

        $conn = parent::get_conexion();
        $placeholders = implode(',', array_fill(0, count($ids_descripciones), '?'));

        $sql = "SELECT DISTINCT id_descripciones_proyecto 
        FROM resumen_documentos_ia 
        WHERE id_descripciones_proyecto IN ($placeholders)
          AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_values($ids_descripciones));

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
    public function existen_resumenes_documento_ia(int $id_descripciones_proyecto): bool
    {
        $conn = parent::get_conexion();
        $sql = "SELECT COUNT(*) AS total FROM resumen_documentos_ia WHERE id_descripciones_proyecto = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id_descripciones_proyecto, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int) $row['total']) > 0;
    }

    public function obtener_resumenes_documento_ia(int $id_descripciones_proyecto): array
    {
        $conn = parent::get_conexion();
        $sql = "SELECT id, documento, resumen, modelo_usado, fech_crea
        FROM resumen_documentos_ia
        WHERE id_descripciones_proyecto = :id AND est=1
        ORDER BY documento ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id_descripciones_proyecto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar_resumen_documento_ia_por_fila(int $id_fila): bool
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE resumen_documentos_ia SET est=0 WHERE id = :id_fila";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_fila", $id_fila, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtener_lock_generacion_ia(int $id_descripciones_proyecto): ?array
    {
        $conn = parent::get_conexion();
        $sql = "SELECT id_descripciones_proyecto, usu_id, fech_inicio
            FROM resumen_ia_lock
            WHERE id_descripciones_proyecto = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id_descripciones_proyecto, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function existe_api_key_activa_x_sector_y_herramienta($sector_id, $id_herramienta)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT COUNT(*) as count FROM api_keys 
        WHERE sector_id = :sector_id 
        AND id_herramienta = :id_herramienta 
        AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        $stmt->bindValue(':id_herramienta', $id_herramienta, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function crear_lock_generacion_ia(int $id_descripciones_proyecto, int $usu_id): bool
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO resumen_ia_lock (id_descripciones_proyecto, usu_id, fech_inicio)
            VALUES (:id, :usu_id, NOW())
            ON DUPLICATE KEY UPDATE usu_id = :usu_id2, fech_inicio = NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id_descripciones_proyecto, PDO::PARAM_INT);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(":usu_id2", $usu_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function liberar_lock_generacion_ia(int $id_descripciones_proyecto): bool
    {
        $conn = parent::get_conexion();
        $sql = "DELETE FROM resumen_ia_lock WHERE id_descripciones_proyecto = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id_descripciones_proyecto, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
