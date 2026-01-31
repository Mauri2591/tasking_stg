<?php

class ToolsHerramientas extends Conexion
{
    /* =========================
       CONSULTAS BASE
       ========================= */

    public function get_tools($cats_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT id,nombre,tipo_ejecucion,handler,engine,path,descripcion
                FROM tools_productos
                WHERE cats_id = :cats_id AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":cats_id", $cats_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_datos_proyecto($id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tipo, host
                FROM hosts
                WHERE id_proyecto_gestionado = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tool_by_id(int $id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT *
                FROM tools_productos
                WHERE id = :id AND est = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       INSERT EJECUCIÓN
       ========================= */

    public function insert_ejecucion(array $data): bool
    {
        $conn = parent::get_conexion();

        $sql = "
        INSERT INTO tools_ejecuciones (
            tool_id,
            id_proyecto_gestionado,
            activo,
            output,
            exit_code,
            ejecutado_por,
            log_file,
            fecha_ejecucion,
            estado,
            est
        ) VALUES (
            :tool_id,
            :id_proyecto,
            :activo,
            :output,
            :exit_code,
            :ejecutado_por,
            :log_file,
            NOW(),
            'RUNNING',
            1
        )";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':tool_id'       => $data['tool_id'],
            ':id_proyecto'   => $data['id_proyecto_gestionado'],
            ':activo'        => $data['activo'],
            ':output'        => $data['output'],
            ':exit_code'     => $data['exit_code'],
            ':ejecutado_por' => $data['usuario'],
            ':log_file'      => $data['log_file'],
        ]);
    }

    /* =========================
       EJECUCIÓN ASÍNCRONA
       ========================= */

    private function ejecutarScriptAsync(string $path, string $host, array $meta): void
    {
        $scriptPath = realpath(OSINT_BASE_PATH . '/' . $path);
        if (!$scriptPath) {
            throw new Exception("Script OSINT no encontrado");
        }

        $safeHost = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $host);
        $logFile  = "/tmp/osint_{$meta['tool_id']}_{$meta['proyecto']}_{$safeHost}.log";

        // INSERTA EJECUCIÓN (RUNNING)
        $this->insert_ejecucion([
            'tool_id' => $meta['tool_id'],
            'id_proyecto_gestionado' => $meta['proyecto'],
            'activo' => $host,
            'output' => '[OSINT] Ejecutando en background…',
            'exit_code' => -1,
            'usuario' => $_SESSION['usu_id'] ?? null,
            'log_file' => $logFile,
        ]);

        // LANZA EN BACKGROUND (SIN BLOQUEAR APACHE)
        $cmd = sprintf(
            'nohup /bin/bash %s %s > %s 2>&1 &',
            escapeshellarg($scriptPath),
            escapeshellarg($host),
            escapeshellarg($logFile)
        );

        exec($cmd);
    }

    /* =========================
       CRT.SH
       ========================= */

    public function ejecutarCrtsh(array $tool, int $idProyecto): array
    {
        $activos = $this->get_datos_proyecto($idProyecto);

        foreach ($activos as $a) {
            if (!in_array($a['tipo'], ['OTRO', 'ACTIVO'])) continue;

            $this->ejecutarScriptAsync(
                $tool['path'],
                $a['host'],
                [
                    'tool_id'  => $tool['id'],
                    'proyecto' => $idProyecto
                ]
            );
        }

        return ['estado' => 'ok', 'mensaje' => 'crt.sh lanzado en background'];
    }

    /* =========================
       GOOGLE DORKS
       ========================= */

    public function ejecutarGoogleDorks(array $tool, int $idProyecto): array
    {
        $activos = $this->get_datos_proyecto($idProyecto);

        foreach ($activos as $a) {
            if (!in_array($a['tipo'], ['OTRO', 'ACTIVO'])) continue;

            $this->ejecutarScriptAsync(
                $tool['path'],
                $a['host'],
                [
                    'tool_id'  => $tool['id'],
                    'proyecto' => $idProyecto
                ]
            );
        }

        return ['estado' => 'ok', 'mensaje' => 'Google Dorks lanzado en background'];
    }

    public function get_ejecuciones_running(): array
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM tools_ejecuciones
            WHERE estado = 'RUNNING'
              AND est = 1";
        return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function finalizar_ejecucion(
        int $id,
        string $output,
        int $exitCode = 0,
        string $estado = 'DONE'
    ): bool {
        $conn = parent::get_conexion();
        $sql = "UPDATE tools_ejecuciones
            SET output = :output,
                exit_code = :exit_code,
                estado = :estado
            WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ':output'    => $output,
            ':exit_code' => $exitCode,
            ':estado'   => $estado,
            ':id'       => $id
        ]);
    }
}
