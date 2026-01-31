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
       EJECUCIÓN ASÍNCRONA REAL
       ========================= */

    private function ejecutarScriptAsync(string $path, string $host): bool
    {
        // No ejecutar OSINT en Windows
        if (stripos(PHP_OS, 'WIN') === 0) {
            return false;
        }

        $scriptPath = realpath(OSINT_BASE_PATH . '/' . $path);

        if (!$scriptPath || !is_file($scriptPath)) {
            error_log('[OSINT] Script no encontrado: ' . OSINT_BASE_PATH . '/' . $path);
            return false;
        }

        // Log independiente por ejecución
        $logFile = '/tmp/osint_' . time() . '_' . rand(1000,9999) . '.log';

        // 👇 CLAVE: nohup + &
        $cmd = sprintf(
            'nohup bash %s %s > %s 2>&1 &',
            escapeshellarg($scriptPath),
            escapeshellarg($host),
            escapeshellarg($logFile)
        );

        error_log('[OSINT] ASYNC CMD: ' . $cmd);

        exec($cmd);

        return true;
    }

    /* =========================
       CRT.SH
       ========================= */

    public function ejecutarCrtsh(array $tool, int $idProyecto): array
    {
        $activos = $this->get_datos_proyecto($idProyecto);
        $lanzado = false;

        foreach ($activos as $activo) {

            if (!in_array($activo['tipo'], ['OTRO', 'ACTIVO'])) {
                continue;
            }

            if ($this->ejecutarScriptAsync($tool['path'], $activo['host'])) {
                $lanzado = true;
            }
        }

        return $lanzado
            ? [
                'estado'  => 'ok',
                'mensaje' => 'crt.sh lanzado en background'
              ]
            : [
                'estado'  => 'error',
                'mensaje' => 'No se pudo lanzar crt.sh'
              ];
    }

    /* =========================
       GOOGLE DORKS
       ========================= */

    public function ejecutarGoogleDorks(array $tool, int $idProyecto): array
    {
        $activos = $this->get_datos_proyecto($idProyecto);
        $lanzado = false;

        foreach ($activos as $activo) {

            if (!in_array($activo['tipo'], ['OTRO', 'ACTIVO'])) {
                continue;
            }

            if ($this->ejecutarScriptAsync($tool['path'], $activo['host'])) {
                $lanzado = true;
            }
        }

        return $lanzado
            ? [
                'estado'  => 'ok',
                'mensaje' => 'Google Dorks lanzado en background'
              ]
            : [
                'estado'  => 'error',
                'mensaje' => 'No se pudo lanzar Google Dorks'
              ];
    }
}
