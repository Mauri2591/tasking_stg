<?php
require_once __DIR__ . "/../../../../../Config/Conexion.php";
require_once __DIR__ . "/../../../../../Config/Config.php";
require_once __DIR__ . "/../../../../../Model/ToolsHerramientas.php";

$tools = new ToolsHerramientas();

$jobs = $tools->get_ejecuciones_running();

foreach ($jobs as $job) {

    $log = $job['log_file'];

    if (!is_file($log)) {
        continue; // sigue corriendo
    }

    // Si el proceso sigue vivo, todavía no lo tocamos
    $pidCheck = trim(shell_exec("pgrep -f " . escapeshellarg($log)));
    if ($pidCheck !== '') {
        continue;
    }

    // Ya terminó → leemos output
    $output = trim(file_get_contents($log));

    if ($output === '') {
        $tools->finalizar_ejecucion(
            $job['id'],
            '[OSINT] Finalizado sin output',
            0,
            'WARN'
        );
    } else {
        $tools->finalizar_ejecucion(
            $job['id'],
            $output,
            0,
            'DONE'
        );
    }
}
