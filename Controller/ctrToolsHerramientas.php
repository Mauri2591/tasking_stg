<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Model/Clases/HtmlPurifier.php";
require_once __DIR__ . "/../Model/ToolsHerramientas.php";
require_once __DIR__ . "/../Model/Clases/Validaciones.php";
require_once __DIR__ . "/../Model/Clases/Headers.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";

$conexion = new Conexion();

$tools = new ToolsHerramientas();
$validacion = new Validaciones();
Headers::get_csp();


switch ($_GET['tools']) {

    case 'get_tools':

        $datos = $tools->get_tools($_POST['cats_id']);
        ?>
        <table class="table-osint">
            <thead>
                <tr>
                    <th>Inteligencia</th>
                    <th>Ejecución</th>
                    <th>Handler</th>
                    <th>Descripción</th>
                    <th>Accion</th>
                    <th>Resultados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $val): ?>
                    <tr>
                        <td title="<?= htmlspecialchars($val['nombre']); ?>">
                            <?= htmlspecialchars($val['nombre']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($val['tipo_ejecucion']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($val['handler']); ?>
                        </td>

                        <td title="<?= htmlspecialchars($val['descripcion']); ?>">
                            <?= htmlspecialchars($val['descripcion']); ?>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-sm btn-primary py-0 px-1 d-inline-flex align-items-center gap-1"
                                onclick="ejecutarHerramienta(<?= (int)$val['id']; ?>)"
                                title="Ejecutar herramienta"
                                id="btnEjecutar<?= (int)$val['id']; ?>">
                                <i class="ri-play-mini-fill" id="iconoEjecutar<?= (int)$val['id']; ?>"></i>
                                <span class="texto-boton">Ejecutar</span>
                            </button>
                        </td>
                        <td> <i class="ri-file-list-fill text-info fw-bold"></i> </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
         </table>
            <?php
        exit;

    case 'get_datos_proyecto':

        echo json_encode(
            $tools->get_datos_proyecto((int)$_POST['id_proyecto_gestionado'])
        );
        exit;

    case 'ejecutar_herramienta':
        try {
            if (empty($_POST['id']) || empty($_POST['id_proyecto_gestionado'])) {
                throw new Exception('Parámetros incompletos');
            }

            $toolId = (int)$_POST['id'];
            $idProyecto = (int)$_POST['id_proyecto_gestionado'];

            $tool = $tools->get_tool_by_id($toolId);
            if (!$tool) {
                throw new Exception('Herramienta no encontrada');
            }

            switch ($tool['engine']) {
                case 'crtsh':
                    $resultado = $tools->ejecutarCrtsh($tool, $idProyecto);
                    break;

              
                default:
                    throw new Exception('Engine OSINT no soportado');
            }

            if ($resultado['estado'] === 'error') {
                http_response_code(500);
            } else {
                http_response_code(200);
            }

            echo json_encode($resultado);
        } catch (Throwable $e) {

            http_response_code(500);
            echo json_encode([
                'estado' => 'error',
                'mensaje' => $e->getMessage()
            ]);
        }
        exit;
        http_response_code(400);
        echo json_encode(['estado' => 'error', 'mensaje' => 'Acción inválida']);
        exit;
}
