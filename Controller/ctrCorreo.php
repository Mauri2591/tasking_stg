<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Correo.php";
$correo = new Correo();
header('Content-Type: application/json; charset=utf-8');
$case = $_GET['correo'] ?? '';

switch ($case) {
    case 'enviar':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            echo json_encode([
                'status' => 'ERROR',
                'error'  => 'ID inválido'
            ]);
            exit;
        }
        $result = $correo->enviarCorreoProyectoFinalizado($id);
        echo json_encode([
            'status' => $result === true ? 'OK' : 'ERROR',
            'error'  => $result === true ? null : $result
        ]);
        exit;

    default:
        echo json_encode([
            'status' => 'ERROR',
            'error'  => 'Acción no válida'
        ]);
        exit;
}
