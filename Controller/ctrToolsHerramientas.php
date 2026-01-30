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
        $htmlOption = '';
        $datos = $tools->get_tools($_POST['cats_id']);
?>
        <table class="table-osint">
            <thead>
                <tr>
                    <th>Inteligencia</th>
                    <th>Ejecución</th>
                    <th>Handler</th>
                    <th>Descripción</th>
                    <th>Botón</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $val): ?>
                    <tr>
                        <td title="<?php echo htmlspecialchars($val['nombre']); ?>">
                            <?php echo htmlspecialchars($val['nombre']); ?>
                        </td>

                        <td title="<?php echo htmlspecialchars($val['tipo_ejecucion']); ?>">
                            <?php echo htmlspecialchars($val['tipo_ejecucion']); ?>
                        </td>

                        <td title="<?php echo htmlspecialchars($val['handler']); ?>">
                            <?php echo htmlspecialchars($val['handler']); ?>
                        </td>

                        <td title="<?php echo htmlspecialchars($val['descripcion']); ?>">
                            <?php echo htmlspecialchars($val['descripcion']); ?>
                        </td>

                        <td class="text-center">
                            <i type="button" title="Ejecutar" class="ri-play-mini-fill fs-22 text-primary" onclick="ejecutarHerramienta('<?php echo htmlspecialchars($val['id'], ENT_QUOTES); ?>')"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

<?php
        break;

    case 'get_datos_proyecto':
        echo json_encode($tools->get_datos_proyecto($_POST['id_proyecto_gestionado']));
        break;

   case 'ejecutar_herramienta':

    // 1️⃣ Validaciones
    if (empty($_POST['id']) || empty($_POST['id_proyecto_gestionado'])) {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'Parámetros incompletos'
        ]);
        exit;
    }

    $toolId = (int) $_POST['id'];
    $idProyecto = (int) $_POST['id_proyecto_gestionado'];

    // 2️⃣ Traer herramienta
    $tool = $tools->get_tool_by_id($toolId);

    if (!$tool) {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'Herramienta no encontrada'
        ]);
        exit;
    }

    // 3️⃣ Enrutador por engine
    switch ($tool['engine']) {

        case 'crtsh':
            $tools->ejecutarCrtsh($tool, $idProyecto);
            break;

        default:
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'Engine OSINT no soportado'
            ]);
            exit;
    }

    echo json_encode([
        'estado' => 'ok',
        'mensaje' => 'Herramienta ejecutada'
    ]);
    exit;

break;


    default:
        break;
}
