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
    if (stripos(PHP_OS, 'WIN') === 0) {
        return false;
    }

    $scriptPath = realpath(OSINT_BASE_PATH . '/' . $path);
    if (!$scriptPath || !is_file($scriptPath)) {
        error_log('[OSINT] Script no encontrado: ' . $path);
        return false;
    }

    // log único por ejecución
    $logFile = '/tmp/osint_' . time() . '_' . rand(1000,9999) . '.log';

    $cmd = sprintf(
        'cd %s && /bin/bash %s %s > %s 2>&1 &',
        escapeshellarg(dirname($scriptPath)),
        escapeshellarg($scriptPath),
        escapeshellarg($host),
        escapeshellarg($logFile)
    );

    error_log('[OSINT] ASYNC CMD: ' . $cmd);

    // importante: NO capturar salida
    exec($cmd);

    return true;
}


    /* =========================
       CRT.SH
       ========================= */

    public function ejecutarCrtsh(array $tool, int $idProyecto): array
{
    $activos = $this->get_datos_proyecto($idProyecto);

    foreach ($activos as $activo) {
        if (!in_array($activo['tipo'], ['OTRO', 'ACTIVO'])) {
            continue;
        }

        $this->ejecutarScriptAsync($tool['path'], $activo['host']);
    }

    return [
        'estado'  => 'ok',
        'mensaje' => 'crt.sh lanzado en background'
    ];
}


    /* =========================
       GOOGLE DORKS
       ========================= */

   public function ejecutarGoogleDorks(array $tool, int $idProyecto): array
{
    $activos = $this->get_datos_proyecto($idProyecto);

    foreach ($activos as $activo) {
        if (!in_array($activo['tipo'], ['OTRO', 'ACTIVO'])) {
            continue;
        }

        $this->ejecutarScriptAsync($tool['path'], $activo['host']);
    }

    return [
        'estado'  => 'ok',
        'mensaje' => 'Google Dorks lanzado en background'
    ];
}

}
