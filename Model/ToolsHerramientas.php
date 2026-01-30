<?php
class ToolsHerramientas extends Conexion
{
    public function get_tools($cats_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tools_productos.id,tools_productos.nombre,tools_productos.tipo_ejecucion,tools_productos.handler,tools_productos.descripcion FROM tools_productos WHERE tools_productos.cats_id=:cats_id AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":cats_id", $cats_id, PDO::PARAM_INT);
        $stmt->execute();
        $resul = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }

    public function get_datos_proyecto($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tipo,host FROM hosts WHERE id_proyecto_gestionado=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $resul = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }

    public function get_tool_by_id(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM tools_productos WHERE id = :id AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_ejecucion(array $data)
    {
        $conn = parent::get_conexion();

        $sql = "INSERT INTO tools_ejecuciones
            (tool_id,
             id_proyecto_gestionado,
             activo,
             output,
             exit_code,
             ejecutado_por,
             fecha_ejecucion,
             est)
            VALUES
            (:tool_id,
             :id_proyecto,
             :activo,
             :output,
             :exit_code,
             :ejecutado_por,
             NOW(),
             1)";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':tool_id', $data['tool_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id_proyecto', $data['id_proyecto_gestionado'], PDO::PARAM_INT);
        $stmt->bindValue(':activo', $data['activo'], PDO::PARAM_STR);
        $stmt->bindValue(':output', $data['output'], PDO::PARAM_STR);
        $stmt->bindValue(':exit_code', $data['exit_code'], PDO::PARAM_INT);
        $stmt->bindValue(':ejecutado_por', $data['usuario'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function ejecutarCrtsh(array $tool, int $idProyecto)
    {
        $conn = parent::get_conexion();
        $activos = $this->get_datos_proyecto($idProyecto);

        foreach ($activos as $activo) {

            if (!in_array($activo['tipo'], ['OTRO', 'ACTIVO'])) {
                continue;
            }

            $host = $activo['host'];
            $scriptPath = realpath(__DIR__ . '/../' . $tool['path']);
            if (!$scriptPath) {
                continue;
            }

            $cmd = 'bash ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($host);

            $out = [];
            $exitCode = null;

            exec($cmd . ' 2>&1', $out, $exitCode);
            $output = trim(implode("\n", $out));

            // ❌ criterio de error real
            if ($exitCode !== 0) {
                continue;
            }

            try {
                $conn->beginTransaction();

                $this->insert_ejecucion([
                    'tool_id' => $tool['id'],
                    'id_proyecto_gestionado' => $idProyecto,
                    'activo' => $host,
                    'output' => $output,
                    'exit_code' => $exitCode,
                    'usuario' => $_SESSION['usu_id'] ?? null
                ]);

                $conn->commit();
            } catch (Throwable $e) {
                $conn->rollBack();
            }
        }
    }
}
