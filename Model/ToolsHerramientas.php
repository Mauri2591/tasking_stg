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

    private function ejecutarScript(string $path, string $host): ?string
    {
        $scriptPath = realpath(OSINT_BASE_PATH . '/' . $path);
        if (!$scriptPath) {
            return null;
        }
        $cmd = 'bash ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($host);
        $out = [];
        $exitCode = null;
        exec($cmd . ' 2>&1', $out, $exitCode);
        if ($exitCode !== 0) {
            return null;
        }
        $output = trim(implode("\n", $out));
        return $output !== '' ? $output : null;
    }


    public function ejecutarCrtsh(array $tool, int $idProyecto): array
    {
        $activos = $this->get_datos_proyecto($idProyecto);
        $huboOk = false;
        $huboWarn = false;

        foreach ($activos as $activo) {

            if (!in_array($activo['tipo'], ['OTRO', 'ACTIVO'])) {
                continue;
            }

            $host = $activo['host'];

            $rawOutput = $this->ejecutarScript($tool['path'], $host);

            if ($rawOutput === null) {
                continue;
            }

            if (str_contains($rawOutput, '<TITLE>crt.sh | ERROR!')) {
                $huboWarn = true;

                $this->insert_ejecucion([
                    'tool_id' => $tool['id'],
                    'id_proyecto_gestionado' => $idProyecto,
                    'activo' => $host,
                    'output' => $rawOutput,
                    'exit_code' => 0,
                    'usuario' => $_SESSION['usu_id'] ?? null
                ]);

                continue;
            }

            $huboOk = true;

            $this->insert_ejecucion([
                'tool_id' => $tool['id'],
                'id_proyecto_gestionado' => $idProyecto,
                'activo' => $host,
                'output' => $rawOutput,
                'exit_code' => 0,
                'usuario' => $_SESSION['usu_id'] ?? null
            ]);
        }
        if ($huboOk) {
            return [
                'estado' => $huboWarn ? 'warn' : 'ok',
                'mensaje' => $huboWarn
                    ? 'Ejecución completada con advertencias (crt.sh inestable)'
                    : 'Ejecución OSINT completada correctamente'
            ];
        }
        if ($huboWarn) {
            return [
                'estado' => 'warn',
                'mensaje' => 'crt.sh no pudo procesar los activos (fuente inestable)'
            ];
        }
        return [
            'estado' => 'error',
            'mensaje' => 'Error interno al ejecutar la herramienta OSINT'
        ];
    }


}
