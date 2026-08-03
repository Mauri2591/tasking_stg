<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../Model/Clases/Openssl.php';

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Project\ListProjectsAction;
use App\Application\Actions\Project\ViewProjectAction;
use App\Middleware\JwtMiddleware;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Domain\User\UserRepository;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

return function (App $app) {


    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('<strong>Bienvenido a la API de Tasking.</strong> Si necesita ayuda, póngase en contacto con el equipo de Ethickal Hacking. Gracias');
        return $response;
    });


    // ******************   INICIO TASKING ***********************
    $app->post('/login', function (Request $request, Response $response) use ($app) {

        $data = $request->getParsedBody();
        $usu_nom = $data['usuario'] ?? '';
        $pass = $data['password'] ?? '';

        $container = $app->getContainer();
        $pdo = $container->get(PDO::class);

        $userRepo = new UserRepository($pdo);
        $user = $userRepo->datosUsuario($usu_nom);

        if ($user && password_verify($pass, $user['usu_pass'])) {
            // Access Token expira en 1 hora
            $payload = [
                'sub' => $user['usu_id'],
                'name' => $user['usu_nom'],
                'iat' => time(),
                'exp' => time() + 3600 // expira en 1 hora
                // 'exp' => time() + 60 // prueba que expire en 1 minuto
            ];

            $accessToken = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

            // Refresh Token (expira en 7 días)
            $refreshToken = bin2hex(random_bytes(32));
            $refreshExp = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60));

            // Guardar refresh token
            $stmt = $pdo->prepare("INSERT INTO refresh_tokens 
            (usu_id, refresh_token, expires_at, est, fecha_creacion)
            VALUES (:id, :rt, :exp, 1, NOW())");
            $stmt->execute([
                'id' => $user['usu_id'],
                'rt' => $refreshToken,
                'exp' => $refreshExp
            ]);

            $response->getBody()->write(json_encode([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Credenciales inválidas']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    });

    /** Genero nuevo Access Token usando Refresh Token*/
    $app->post('/refresh', function (Request $request, Response $response) use ($app) {
        $data = $request->getParsedBody();
        $refreshToken = $data['refresh_token'] ?? '';

        $container = $app->getContainer();
        $pdo = $container->get(PDO::class);

        $stmt = $pdo->prepare("SELECT rt.usu_id, u.usu_nom, rt.expires_at
            FROM refresh_tokens rt
            INNER JOIN tm_usuario u ON rt.usu_id = u.usu_id
            WHERE rt.refresh_token = :rt AND rt.est = 1
        ");
        $stmt->execute(['rt' => $refreshToken]);
        $user = $stmt->fetch();

        if ($user && $user['expires_at'] > time()) {
            // Generar nuevo Access Token
            $payload = [
                'sub' => $user['usu_id'],
                'name' => $user['usu_nom'],
                'iat' => time(),
                'exp' => time() + 3600
            ];
            $newAccessToken = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

            $response->getBody()->write(json_encode(['access_token' => $newAccessToken]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Refresh token inválido o expirado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    });

    /** Elimino el Refresh Token */
    $app->post('/logout', function (Request $request, Response $response) use ($app) {
        $data = $request->getParsedBody();
        $refreshToken = $data['refresh_token'] ?? '';

        $container = $app->getContainer();
        $pdo = $container->get(PDO::class);

        // Validar que el token existe y está activo
        $stmt = $pdo->prepare("SELECT * FROM refresh_tokens WHERE refresh_token = :rt AND est = 1");
        $stmt->execute(['rt' => $refreshToken]);
        if (!$stmt->fetch()) {
            $response->getBody()->write(json_encode(['error' => 'Token inválido o ya revocado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Revocar el token
        $stmt = $pdo->prepare("UPDATE refresh_tokens SET est = 0 WHERE refresh_token = :rt");
        $stmt->execute(['rt' => $refreshToken]);

        $response->getBody()->write(json_encode(['message' => 'Logout exitoso']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/clientes', function (Request $request, Response $response) use ($app) {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $token = $matches[1];
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token inválido o expirado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }
        // 2. Ejecutar la consulta
        $pdo = $app->getContainer()->get(PDO::class);
        $sql = "SELECT client_id,client_rs,client_cuit,client_correo,client_tel, clientes.est 
            AS estado, IF(clientes.est = 1,'ACTIVO','INACTIVO') 
            AS estado_descripcion, tm_pais.pais_id, tm_pais.pais_nombre 
            FROM clientes 
            INNER JOIN tm_pais ON clientes.pais_id=tm_pais.pais_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->get('/proyectosTasking', function (Request $request, Response $response) use ($app) {
        // 1. Validar token JWT
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token inválido o expirado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        // 2. Ejecutar la consulta
        $pdo = $app->getContainer()->get(PDO::class);
        $sql = "SELECT 
        pg.id AS id_proyecto,
        clientes.client_id AS id_cliente,
        clientes.client_rs AS nombre_cliente,
        pg.titulo AS titulo_proyecto,
        pg.refProy AS referencia,
        pg.prioridad_id AS id_prioridad,
        prioridad.prioridad,
        prr.posicion_recurrencia AS recurrencia,
        tm_pais.pais_nombre AS pais_nombre,
        tm_pais.pais_id AS pais_id,
        IF(workshop.est = 1,'SI','NO') AS workshop,
        IF(pr.id IS NOT NULL,'SI','NO') AS rechequeo,
        IF(pg.descripcion = '', NULL, pg.descripcion) AS descripcion_proyecto,
        IF(pg.fech_inicio = '', NULL, pg.fech_inicio) AS fecha_inicio,
        IF(pg.fech_fin = '', NULL, pg.fech_fin) AS fecha_fin,
        pg.fech_vantive,
        GROUP_CONCAT(DISTINCT up.usu_asignado) AS ids_usuarios_asignados,
        GROUP_CONCAT(DISTINCT tu.usu_nom) AS nombres_usuarios_asignados,
        GROUP_CONCAT(DISTINCT tu.usu_correo SEPARATOR ',') AS correo_usuario,
        pg.estados_id AS id_estado_proyecto,
        te.estados_nombre AS nombre_estado_proyecto,
        tc.cat_id AS producto_id,
        tc.cat_nom AS producto_nombre,
        tm_subcategoria.cats_id AS tipo_id,
        tm_subcategoria.cats_nom AS tipo_nombre,
        d.hs_dimensionadas,
        CONCAT(
        '{',
        '\"ips\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'IP' THEN CONCAT('\"', h.host, '\"') END), ''), '],',
        '\"urls\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'URL' THEN CONCAT('\"', h.host, '\"') END), ''), '],',
        '\"otros\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo NOT IN ('IP','URL') THEN CONCAT('\"', h.host, '\"') END), ''), ']',
        '}'
            ) AS hosts
            FROM proyecto_gestionado pg
            LEFT JOIN usuario_proyecto up ON pg.id = up.id_proyecto_gestionado
            LEFT JOIN tm_usuario tu ON up.usu_asignado = tu.usu_id
            LEFT JOIN tm_estados te ON pg.estados_id = te.estados_id
            LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
            LEFT JOIN tm_subcategoria ON pg.cats_id = tm_subcategoria.cats_id
            LEFT JOIN proyecto_rechequeo pr ON pg.id = pr.id_proyecto_gestionado
            LEFT JOIN proyecto_recurrencia prr ON pg.id = prr.id_proyecto_gestionado
            LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado
            LEFT JOIN hosts h ON pg.id = h.id_proyecto_gestionado AND h.est = 1
            LEFT JOIN workshop ON pg.id = workshop.id_proyecto_gestionado

            INNER JOIN proyecto_cantidad_servicios pcs 
                ON pg.id_proyecto_cantidad_servicios = pcs.id
            INNER JOIN proyectos 
                ON pcs.proy_id = proyectos.proy_id
            INNER JOIN clientes 
                ON proyectos.client_id = clientes.client_id
            INNER JOIN tm_pais 
                ON clientes.pais_id = tm_pais.pais_id
            INNER JOIN prioridad 
                ON pg.prioridad_id = prioridad.id
            WHERE pg.estados_id NOT IN (14,15,16,17) AND pg.sector_id = 1
            AND tc.cat_id <> 78
            AND tm_subcategoria.cats_id NOT IN (79,80,82)
            GROUP BY pg.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            $response->getBody()->write(json_encode(['error' => 'Sin datos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // 3. Transformar datos
        foreach ($rows as &$row) {
            // Traer IPs, URLs y otros por separado desde la BD
            $stmtHosts = $pdo->prepare("SELECT tipo, host FROM hosts WHERE id_proyecto_gestionado = :id AND est = 1");
            $stmtHosts->execute([':id' => $row['id_proyecto']]);
            $hostsRows = $stmtHosts->fetchAll(PDO::FETCH_ASSOC);

            $row['hosts'] = ['ips' => [], 'urls' => [], 'otros' => []];
            foreach ($hostsRows as $h) {
                if ($h['tipo'] === 'IP')          $row['hosts']['ips'][]   = $h['host'];
                elseif ($h['tipo'] === 'URL')     $row['hosts']['urls'][]  = $h['host'];
                else                              $row['hosts']['otros'][] = $h['host'];
            }
            // Cliente
            $row['cliente'] = [
                'id' => $row['id_cliente'],
                'nombre' => $row['nombre_cliente']
            ];

            //Paises
            $row['pais'] = [
                'id' => $row['pais_id'],
                'nombre' => $row['pais_nombre']
            ];

            // Usuarios
            $row['usuarios'] = [
                'ids' => $row['ids_usuarios_asignados'] ? explode(',', $row['ids_usuarios_asignados']) : [],
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : [],
                'correos' => $row['correo_usuario'] ? explode(',', $row['correo_usuario']) : []
            ];

            // Producto
            $row['producto'] = [
                'id' => $row['producto_id'],
                'nombre' => $row['producto_nombre']
            ];

            // Tipo
            $row['tipo'] = [
                'id' => $row['tipo_id'],
                'nombre' => $row['tipo_nombre']
            ];

            $row['prioridad'] = [
                'id' => $row['id_prioridad'],
                'nombre' => $row['prioridad']
            ];

            //Estados
            $row['estado'] = [
                'id' => $row['id_estado_proyecto'],
                'nombre' => $row['nombre_estado_proyecto']
            ];

            // Eliminar campos planos
            unset(
                $row['id_cliente'],
                $row['nombre_cliente'],
                $row['ids_usuarios_asignados'],
                $row['nombres_usuarios_asignados'],
                $row['producto_id'],
                $row['producto_nombre'],
                $row['tipo_id'],
                $row['tipo_nombre'],
                $row['id_estado_proyecto'],
                $row['nombre_estado_proyecto'],
                $row['id_prioridad'],
                $row['id_pais'],
                $row['pais_nombre'],
                $row['correo_usuario']
            );
        }

        $response->getBody()->write(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->get('/proyectosTaskingEhAbiertos', function (Request $request, Response $response) use ($app) {
        // 1. Validar token JWT
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token inválido o expirado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        // 2. Ejecutar la consulta
        $pdo = $app->getContainer()->get(PDO::class);
        $sql = "SELECT 
            pg.id AS id_proyecto,
            clientes.client_id AS id_cliente,
            clientes.client_rs AS nombre_cliente,
            pg.titulo AS titulo_proyecto,
            pg.refProy AS referencia,
            pg.prioridad_id AS id_prioridad,
            prioridad.prioridad,
            prr.posicion_recurrencia AS recurrencia,
            IF(workshop.est = 1,'SI','NO') AS workshop,
            IF(pr.id IS NOT NULL,'SI','NO') AS rechequeo,
            IF(pg.descripcion = '', NULL, pg.descripcion) AS descripcion_proyecto,
            IF(pg.fech_inicio = '', NULL, pg.fech_inicio) AS fecha_inicio,
            IF(pg.fech_fin = '', NULL, pg.fech_fin) AS fecha_fin,
            pg.fech_vantive,
            GROUP_CONCAT(DISTINCT up.usu_asignado) AS ids_usuarios_asignados,
            GROUP_CONCAT(DISTINCT tu.usu_nom) AS nombres_usuarios_asignados,
            GROUP_CONCAT(DISTINCT tu.usu_correo SEPARATOR ',') AS correo_usuario,
            pg.estados_id AS id_estado_proyecto,
            te.estados_nombre AS nombre_estado_proyecto,
            tc.cat_id AS producto_id,
            tc.cat_nom AS producto_nombre,
            tm_subcategoria.cats_id AS tipo_id,
            tm_subcategoria.cats_nom AS tipo_nombre,
            d.hs_dimensionadas,
            CONCAT(
                '{',
                '\"ips\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'IP' THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), '],',
                '\"urls\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'URL' THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), '],',
                '\"otros\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo NOT IN ('IP','URL') THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), ']',
                '}'
            ) AS hosts,
         tm_pais.pais_id,
         tm_pais.pais_nombre
        FROM proyecto_gestionado pg
        LEFT JOIN usuario_proyecto up ON pg.id = up.id_proyecto_gestionado
        LEFT JOIN tm_usuario tu ON up.usu_asignado = tu.usu_id
        LEFT JOIN tm_estados te ON pg.estados_id = te.estados_id
        LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
        LEFT JOIN tm_subcategoria ON pg.cats_id = tm_subcategoria.cats_id
        LEFT JOIN proyecto_rechequeo pr ON pg.id = pr.id_proyecto_gestionado
        LEFT JOIN proyecto_recurrencia prr ON pg.id = prr.id_proyecto_gestionado
        LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado
        LEFT JOIN hosts h ON pg.id = h.id_proyecto_gestionado AND h.est = 1
        LEFT JOIN workshop ON pg.id=workshop.id_proyecto_gestionado
        LEFT JOIN sectores on pg.sector_id=sectores.sector_id
        INNER JOIN prioridad ON pg.prioridad_id=prioridad.id
        INNER JOIN proyecto_cantidad_servicios pcs ON pg.id_proyecto_cantidad_servicios = pcs.id
        INNER JOIN proyectos ON pcs.proy_id = proyectos.proy_id
        INNER JOIN clientes ON proyectos.client_id = clientes.client_id
        INNER JOIN tm_pais ON clientes.pais_id=tm_pais.pais_id
        WHERE pg.estados_id IN(2) AND sectores.sector_id=1 AND sectores.sector_id = 1
        AND tc.cat_id <> 78
        AND tm_subcategoria.cats_id NOT IN (79,80,82)
        GROUP BY pg.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            $response->getBody()->write(json_encode(['error' => 'Sin datos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // 3. Transformar datos
        foreach ($rows as &$row) {
            // Traer IPs, URLs y otros por separado desde la BD
            $stmtHosts = $pdo->prepare("SELECT tipo, host FROM hosts WHERE id_proyecto_gestionado = :id AND est = 1");
            $stmtHosts->execute([':id' => $row['id_proyecto']]);
            $hostsRows = $stmtHosts->fetchAll(PDO::FETCH_ASSOC);

            $row['hosts'] = ['ips' => [], 'urls' => [], 'otros' => []];
            foreach ($hostsRows as $h) {
                if ($h['tipo'] === 'IP')          $row['hosts']['ips'][]   = $h['host'];
                elseif ($h['tipo'] === 'URL')     $row['hosts']['urls'][]  = $h['host'];
                else                              $row['hosts']['otros'][] = $h['host'];
            }

            // Cliente
            $row['cliente'] = [
                'id' => $row['id_cliente'],
                'nombre' => $row['nombre_cliente']
            ];

            //Pais
            $row['pais'] = [
                'id' => $row['pais_id'],
                'nombre' => $row['pais_nombre']
            ];

            // Usuarios
            $row['usuarios'] = [
                'ids' => $row['ids_usuarios_asignados'] ? explode(',', $row['ids_usuarios_asignados']) : [],
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : [],
                'correos' => $row['correo_usuario'] ? explode(',', $row['correo_usuario']) : []
            ];

            // Producto
            $row['producto'] = [
                'id' => $row['producto_id'],
                'nombre' => $row['producto_nombre']
            ];

            // Tipo
            $row['tipo'] = [
                'id' => $row['tipo_id'],
                'nombre' => $row['tipo_nombre']
            ];

            $row['prioridad'] = [
                'id' => $row['id_prioridad'],
                'nombre' => $row['prioridad']
            ];

            //Estados
            $row['estado'] = [
                'id' => $row['id_estado_proyecto'],
                'nombre' => $row['nombre_estado_proyecto']
            ];

            // Eliminar campos planos
            unset(
                $row['id_cliente'],
                $row['nombre_cliente'],
                $row['ids_usuarios_asignados'],
                $row['nombres_usuarios_asignados'],
                $row['producto_id'],
                $row['producto_nombre'],
                $row['tipo_id'],
                $row['tipo_nombre'],
                $row['id_estado_proyecto'],
                $row['nombre_estado_proyecto'],
                $row['id_prioridad'],
                $row['pais_id'],
                $row['pais_nombre'],
                $row['correo_usuario']
            );
        }
        $response->getBody()->write(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->get('/proyectosTaskingEhPorEstado/{estados_id}', function (Request $request, Response $response, array $args) use ($app) {
        // 1. Validar token JWT
        $authHeader = $request->getHeaderLine('Authorization');
        // Preparo el parametro 
        $estados_id = (int) $args['estados_id'] ?? 0;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token inválido o expirado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        // 2. Ejecutar la consulta
        $pdo = $app->getContainer()->get(PDO::class);
        $sql = "SELECT 
            pg.id AS id_proyecto,
            clientes.client_id AS id_cliente,
            clientes.client_rs AS nombre_cliente,
            pg.titulo AS titulo_proyecto,
            pg.refProy AS referencia,
            pg.prioridad_id AS id_prioridad,
            prioridad.prioridad,
            prr.posicion_recurrencia AS recurrencia,
            IF(workshop.est = 1,'SI','NO') AS workshop,
            IF(pr.id IS NOT NULL,'SI','NO') AS rechequeo,
            IF(pg.descripcion = '', NULL, pg.descripcion) AS descripcion_proyecto,
            IF(pg.fech_inicio = '', NULL, pg.fech_inicio) AS fecha_inicio,
            IF(pg.fech_fin = '', NULL, pg.fech_fin) AS fecha_fin,
            pg.fech_vantive,
            GROUP_CONCAT(DISTINCT up.usu_asignado) AS ids_usuarios_asignados,
            GROUP_CONCAT(DISTINCT tu.usu_nom) AS nombres_usuarios_asignados,
            GROUP_CONCAT(DISTINCT tu.usu_correo SEPARATOR ',') AS correo_usuario,
            pg.estados_id AS id_estado_proyecto,
            te.estados_nombre AS nombre_estado_proyecto,
            tc.cat_id AS producto_id,
            tc.cat_nom AS producto_nombre,
            tm_subcategoria.cats_id AS tipo_id,
            tm_subcategoria.cats_nom AS tipo_nombre,
            d.hs_dimensionadas,
            CONCAT(
                '{',
                '\"ips\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'IP' THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), '],',
                '\"urls\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'URL' THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), '],',
                '\"otros\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo NOT IN ('IP','URL') THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), ']',
                '}'
            ) AS hosts,
            tm_pais.pais_id,
            tm_pais.pais_nombre
        FROM proyecto_gestionado pg
        LEFT JOIN usuario_proyecto up ON pg.id = up.id_proyecto_gestionado
        LEFT JOIN tm_usuario tu ON up.usu_asignado = tu.usu_id
        LEFT JOIN tm_estados te ON pg.estados_id = te.estados_id
        LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
        LEFT JOIN tm_subcategoria ON pg.cats_id = tm_subcategoria.cats_id
        LEFT JOIN proyecto_rechequeo pr ON pg.id = pr.id_proyecto_gestionado
        LEFT JOIN proyecto_recurrencia prr ON pg.id = prr.id_proyecto_gestionado
        LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado
        LEFT JOIN hosts h ON pg.id = h.id_proyecto_gestionado AND h.est = 1
        LEFT JOIN workshop ON pg.id=workshop.id_proyecto_gestionado
        LEFT JOIN sectores on pg.sector_id=sectores.sector_id
        INNER JOIN prioridad ON pg.prioridad_id=prioridad.id
        INNER JOIN proyecto_cantidad_servicios pcs ON pg.id_proyecto_cantidad_servicios = pcs.id
        INNER JOIN proyectos ON pcs.proy_id = proyectos.proy_id
        INNER JOIN clientes ON proyectos.client_id = clientes.client_id
        INNER JOIN tm_pais ON clientes.pais_id=tm_pais.pais_id
        WHERE pg.estados_id = :estados_id AND sectores.sector_id=1   AND sectores.sector_id = 1
        AND tc.cat_id <> 78
        AND tm_subcategoria.cats_id NOT IN (79,80,82)
        GROUP BY pg.id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":estados_id", $estados_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            $response->getBody()->write(json_encode(['error' => 'Sin datos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // 3. Transformar datos
        foreach ($rows as &$row) {
            // Traer IPs, URLs y otros por separado desde la BD
            $stmtHosts = $pdo->prepare("SELECT tipo, host FROM hosts WHERE id_proyecto_gestionado = :id AND est = 1");
            $stmtHosts->execute([':id' => $row['id_proyecto']]);
            $hostsRows = $stmtHosts->fetchAll(PDO::FETCH_ASSOC);

            $row['hosts'] = ['ips' => [], 'urls' => [], 'otros' => []];
            foreach ($hostsRows as $h) {
                if ($h['tipo'] === 'IP')          $row['hosts']['ips'][]   = $h['host'];
                elseif ($h['tipo'] === 'URL')     $row['hosts']['urls'][]  = $h['host'];
                else                              $row['hosts']['otros'][] = $h['host'];
            }

            // Cliente
            $row['cliente'] = [
                'id' => $row['id_cliente'],
                'nombre' => $row['nombre_cliente']
            ];

            // Pais
            $row['pais'] = [
                'id' => $row['pais_id'],
                'nombre' => $row['pais_nombre']
            ];

            // Usuarios
            $row['usuarios'] = [
                'ids' => $row['ids_usuarios_asignados'] ? explode(',', $row['ids_usuarios_asignados']) : [],
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : [],
                'correos' => $row['correo_usuario'] ? explode(',', $row['correo_usuario']) : []
            ];

            // Producto
            $row['producto'] = [
                'id' => $row['producto_id'],
                'nombre' => $row['producto_nombre']
            ];

            // Tipo
            $row['tipo'] = [
                'id' => $row['tipo_id'],
                'nombre' => $row['tipo_nombre']
            ];

            $row['prioridad'] = [
                'id' => $row['id_prioridad'],
                'nombre' => $row['prioridad']
            ];

            //Estados
            $row['estado'] = [
                'id' => $row['id_estado_proyecto'],
                'nombre' => $row['nombre_estado_proyecto']
            ];

            // Eliminar campos planos
            unset(
                $row['id_cliente'],
                $row['nombre_cliente'],
                $row['ids_usuarios_asignados'],
                $row['nombres_usuarios_asignados'],
                $row['producto_id'],
                $row['producto_nombre'],
                $row['tipo_id'],
                $row['tipo_nombre'],
                $row['id_estado_proyecto'],
                $row['nombre_estado_proyecto'],
                $row['id_prioridad'],
                $row['pais_id'],
                $row['pais_nombre'],
                $row['correo_usuario']
            );
        }
        $response->getBody()->write(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->get('/proyectosTaskingEhPorId/{id}', function (Request $request, Response $response, array $args) use ($app) {
        // 1. Validar token JWT
        $authHeader = $request->getHeaderLine('Authorization');

        // Preparo el parametro 
        $id = (int) $args['id'] ?? 0;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token inválido o expirado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        // 2. Ejecutar la consulta
        $pdo = $app->getContainer()->get(PDO::class);
        $sql = "SELECT 
            pg.id AS id_proyecto,
            clientes.client_id AS id_cliente,
            clientes.client_rs AS nombre_cliente,
            pg.titulo AS titulo_proyecto,
            pg.refProy AS referencia,
            pg.prioridad_id AS id_prioridad,
            prioridad.prioridad,
            prr.posicion_recurrencia AS recurrencia,
            IF(workshop.est = 1,'SI','NO') AS workshop,
            IF(pr.id IS NOT NULL,'SI','NO') AS rechequeo,
            IF(pg.descripcion = '', NULL, pg.descripcion) AS descripcion_proyecto,
            IF(pg.fech_inicio = '', NULL, pg.fech_inicio) AS fecha_inicio,
            IF(pg.fech_fin = '', NULL, pg.fech_fin) AS fecha_fin,
            pg.fech_vantive,
            GROUP_CONCAT(DISTINCT up.usu_asignado) AS ids_usuarios_asignados,
            GROUP_CONCAT(DISTINCT tu.usu_nom) AS nombres_usuarios_asignados,
            GROUP_CONCAT(DISTINCT tu.usu_correo SEPARATOR ',') AS correo_usuario,
            pg.estados_id AS id_estado_proyecto,
            te.estados_nombre AS nombre_estado_proyecto,
            tc.cat_id AS producto_id,
            tc.cat_nom AS producto_nombre,
            tm_subcategoria.cats_id AS tipo_id,
            tm_subcategoria.cats_nom AS tipo_nombre,
            d.hs_dimensionadas,
            CONCAT(
                '{',
                '\"ips\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'IP' THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), '],',
                '\"urls\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'URL' THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), '],',
                '\"otros\": [', IFNULL(GROUP_CONCAT(DISTINCT CASE WHEN h.tipo NOT IN ('IP','URL') THEN CONCAT('\"', h.host, '\"') END SEPARATOR ','), ''), ']',
                '}'
            ) AS hosts,
            tm_pais.pais_id,
            tm_pais.pais_nombre
        FROM proyecto_gestionado pg
        LEFT JOIN usuario_proyecto up ON pg.id = up.id_proyecto_gestionado
        LEFT JOIN tm_usuario tu ON up.usu_asignado = tu.usu_id
        LEFT JOIN tm_estados te ON pg.estados_id = te.estados_id
        LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
        LEFT JOIN tm_subcategoria ON pg.cats_id = tm_subcategoria.cats_id
        LEFT JOIN proyecto_rechequeo pr ON pg.id = pr.id_proyecto_gestionado
        LEFT JOIN proyecto_recurrencia prr ON pg.id = prr.id_proyecto_gestionado
        LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado
        LEFT JOIN hosts h ON pg.id = h.id_proyecto_gestionado AND h.est = 1
        LEFT JOIN workshop ON pg.id=workshop.id_proyecto_gestionado
        LEFT JOIN sectores on pg.sector_id=sectores.sector_id
        INNER JOIN prioridad ON pg.prioridad_id=prioridad.id
        INNER JOIN proyecto_cantidad_servicios pcs ON pg.id_proyecto_cantidad_servicios = pcs.id
        INNER JOIN proyectos ON pcs.proy_id = proyectos.proy_id
        INNER JOIN clientes ON proyectos.client_id = clientes.client_id
        INNER JOIN tm_pais ON clientes.pais_id=tm_pais.pais_id
        WHERE pg.id = :id AND sectores.sector_id=1 AND sectores.sector_id = 1
        AND tc.cat_id <> 78
        AND tm_subcategoria.cats_id NOT IN (79,80,82)
        GROUP BY pg.id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            $response->getBody()->write(json_encode(['error' => 'Sin datos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // 3. Transformar datos
        foreach ($rows as &$row) {
            // Traer IPs, URLs y otros por separado desde la BD
            $stmtHosts = $pdo->prepare("SELECT tipo, host FROM hosts WHERE id_proyecto_gestionado = :id AND est = 1");
            $stmtHosts->execute([':id' => $row['id_proyecto']]);
            $hostsRows = $stmtHosts->fetchAll(PDO::FETCH_ASSOC);

            $row['hosts'] = ['ips' => [], 'urls' => [], 'otros' => []];
            foreach ($hostsRows as $h) {
                if ($h['tipo'] === 'IP')          $row['hosts']['ips'][]   = $h['host'];
                elseif ($h['tipo'] === 'URL')     $row['hosts']['urls'][]  = $h['host'];
                else                              $row['hosts']['otros'][] = $h['host'];
            }

            // Cliente
            $row['cliente'] = [
                'id' => $row['id_cliente'],
                'nombre' => $row['nombre_cliente']
            ];
            // Pais
            $row['pais'] = [
                'id' => $row['pais_id'],
                'nombre' => $row['pais_nombre']
            ];

            // Usuarios
            $row['usuarios'] = [
                'ids' => $row['ids_usuarios_asignados'] ? explode(',', $row['ids_usuarios_asignados']) : [],
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : [],
                'correos' => $row['correo_usuario'] ? explode(',', $row['correo_usuario']) : []
            ];

            // Producto
            $row['producto'] = [
                'id' => $row['producto_id'],
                'nombre' => $row['producto_nombre']
            ];

            // Tipo
            $row['tipo'] = [
                'id' => $row['tipo_id'],
                'nombre' => $row['tipo_nombre']
            ];

            $row['prioridad'] = [
                'id' => $row['id_prioridad'],
                'nombre' => $row['prioridad']
            ];

            //Estados
            $row['estado'] = [
                'id' => $row['id_estado_proyecto'],
                'nombre' => $row['nombre_estado_proyecto']
            ];

            // Eliminar campos planos
            unset(
                $row['id_cliente'],
                $row['nombre_cliente'],
                $row['ids_usuarios_asignados'],
                $row['nombres_usuarios_asignados'],
                $row['producto_id'],
                $row['producto_nombre'],
                $row['tipo_id'],
                $row['tipo_nombre'],
                $row['id_estado_proyecto'],
                $row['nombre_estado_proyecto'],
                $row['id_prioridad'],
                $row['pais_id'],
                $row['pais_nombre'],
                $row['correo_usuario'],
            );
        }
        $response->getBody()->write(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    })->add(JwtMiddleware::class);

    $app->group('/projects', function (Group $group) {
        $group->get('', ListProjectsAction::class);
        $group->get('/{id}', ViewProjectAction::class);
    })->add(JwtMiddleware::class);
    // ******************   FIN TASKING ***********************


    // ******************   INICIO TIMASUMMARY ***********************

    //Tareas de usuarios
    $app->get('/cargas-tareas', function (Request $request, Response $response) use ($app) {

        $apiKeyPlana = $request->getHeaderLine('X-API-KEY');
        if (!$apiKeyPlana) {
            $response->getBody()->write(json_encode(["error" => "API Key requerida"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $pdo = $app->getContainer()->get(PDO::class);

        $keys = $pdo->query("SELECT api_key, sector_id FROM api_keys WHERE est = 1")
            ->fetchAll(PDO::FETCH_ASSOC);

        $sector_id = null;
        foreach ($keys as $row) {
            if (hash_equals(Openssl::get_ssl_decrypt($row['api_key']), $apiKeyPlana)) {
                $sector_id = (int)$row['sector_id'];
                break;
            }
        }

        if (!$sector_id) {
            $response->getBody()->write(json_encode(["error" => "API Key inválida"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }
        if ($sector_id === 4) {
            $sql = "SELECT
            CONCAT(u.usu_nom, ' ', u.usu_ape) AS colaborador,
            COALESCE(sp.sector_nombre, su.sector_nombre) AS area,
            c.cat_nom AS producto,
            t.nombre AS tarea,
            ts.fecha,
            ts.hora_desde AS hora_inicio,
            ts.hora_hasta AS hora_fin,
            ts.horas_consumidas AS HT,
            IF(ts.horas_consumidas < 0, ts.horas_consumidas, NULL) AS HN,
            CASE WHEN ts.es_telecom = 'Telecom' THEN 'TELECOM' ELSE cli.client_rs END AS cliente,
            CASE WHEN ts.es_telecom = 'Telecom' THEN 'ARGENTINA' ELSE p.pais_nombre END AS pais,
            ts.descripcion
        FROM timesummary_carga ts
        LEFT JOIN tm_usuario u ON u.usu_id = ts.usu_id
        LEFT JOIN tm_categoria c ON c.cat_id = ts.id_producto
        LEFT JOIN tareas t ON t.id = ts.id_tarea
        LEFT JOIN proyecto_gestionado pg ON pg.id = ts.id_proyecto_gestionado
        LEFT JOIN sectores sp ON sp.sector_id = pg.sector_id
        LEFT JOIN sectores su ON su.sector_id = u.sector_id
        LEFT JOIN proyecto_cantidad_servicios pcs ON pcs.id = pg.id_proyecto_cantidad_servicios
        LEFT JOIN proyectos pr ON pr.proy_id = pcs.proy_id
        LEFT JOIN clientes cli ON cli.client_id = pr.client_id
        LEFT JOIN tm_pais p ON p.pais_id = cli.pais_id
        WHERE ts.est = 1
        ORDER BY ts.fecha DESC";
            $stmt = $pdo->prepare($sql);
        } else {
            $sql = "SELECT
        CONCAT(u.usu_nom, ' ', u.usu_ape) AS colaborador,
        COALESCE(sp.sector_nombre, su.sector_nombre) AS area,
        c.cat_nom AS producto,
        t.nombre AS tarea,
        ts.fecha,
        ts.hora_desde AS hora_inicio,
        ts.hora_hasta AS hora_fin,
        ts.horas_consumidas AS HT,
        IF(ts.horas_consumidas < 0, ts.horas_consumidas, NULL) AS HN,
        CASE
            WHEN ts.es_telecom = 'Telecom' THEN 'TELECOM'
            ELSE cli.client_rs
        END AS cliente,
        CASE
            WHEN ts.es_telecom = 'Telecom' THEN 'ARGENTINA'
            ELSE p.pais_nombre
        END AS pais,
        ts.descripcion
        FROM timesummary_carga ts
        LEFT JOIN tm_usuario u ON u.usu_id = ts.usu_id
        LEFT JOIN tm_categoria c ON c.cat_id = ts.id_producto
        LEFT JOIN tareas t ON t.id = ts.id_tarea
        LEFT JOIN proyecto_gestionado pg ON pg.id = ts.id_proyecto_gestionado
        LEFT JOIN sectores sp ON sp.sector_id = pg.sector_id
        LEFT JOIN sectores su ON su.sector_id = u.sector_id
        LEFT JOIN proyecto_cantidad_servicios pcs ON pcs.id = pg.id_proyecto_cantidad_servicios
        LEFT JOIN proyectos pr ON pr.proy_id = pcs.proy_id
        LEFT JOIN clientes cli ON cli.client_id = pr.client_id
        LEFT JOIN tm_pais p ON p.pais_id = cli.pais_id
        WHERE ts.est = 1
        AND COALESCE(sp.sector_id, su.sector_id) IN (:sector_id, 5)
        ORDER BY ts.fecha DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $response->getBody()->write(
            json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE)
        );
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    //Cross-Sell
    $app->get('/cross-sell', function (Request $request, Response $response) use ($app) {

        $apiKeyPlana = $request->getHeaderLine('X-API-KEY');
        if (!$apiKeyPlana) {
            $response->getBody()->write(json_encode(["error" => "API Key requerida"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $pdo = $app->getContainer()->get(PDO::class);

        $keys = $pdo->query("SELECT api_key, sector_id FROM api_keys WHERE est = 1")
            ->fetchAll(PDO::FETCH_ASSOC);

        $sector_id = null;
        foreach ($keys as $row) {
            if (hash_equals(Openssl::get_ssl_decrypt($row['api_key']), $apiKeyPlana)) {
                $sector_id = (int)$row['sector_id'];
                break;
            }
        }

        if (!$sector_id) {
            $response->getBody()->write(json_encode(["error" => "API Key inválida"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }
        $sql = "SELECT 
            c.client_rs AS cliente,
            c.client_cuit AS cuit,
            pg.fech_crea AS fecha,
            GROUP_CONCAT(DISTINCT s.sector_nombre ORDER BY s.sector_nombre SEPARATOR ', ') AS sectores_contratados,
            GROUP_CONCAT(DISTINCT sf.sector_nombre ORDER BY sf.sector_nombre SEPARATOR ', ') AS sectores_faltantes
            FROM clientes c
            INNER JOIN proyectos p ON p.client_id = c.client_id
            INNER JOIN proyecto_cantidad_servicios pcs ON pcs.proy_id = p.proy_id
            INNER JOIN proyecto_gestionado pg ON pg.id_proyecto_cantidad_servicios = pcs.id
            INNER JOIN sectores s ON s.sector_id = pg.sector_id
            INNER JOIN sectores sf ON sf.sector_id IN (1,2,3)
                AND sf.sector_id NOT IN (
                    SELECT DISTINCT pg2.sector_id
                    FROM proyecto_gestionado pg2
                    INNER JOIN proyecto_cantidad_servicios pcs2 ON pcs2.id = pg2.id_proyecto_cantidad_servicios
                    INNER JOIN proyectos p2 ON p2.proy_id = pcs2.proy_id
                    WHERE p2.client_id = c.client_id
                    AND pg2.estados_id IN (1,2,3,4,14)
                    AND pg2.sector_id IN (1,2,3)
                    AND YEAR(pg2.fech_crea) = YEAR(CURDATE())
                )
            WHERE pg.estados_id IN (1,2,3,4,14)
            AND pg.sector_id IN (1,2,3)
            AND YEAR(pg.fech_crea) = YEAR(CURDATE())
            GROUP BY c.client_id, c.client_rs
            HAVING COUNT(DISTINCT pg.sector_id) < 3
            ORDER BY c.client_rs";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $response->getBody()->write(
            json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE)
        );
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });
    // ******************   FIN TIMASUMMARY ***********************

    //************************** OAUTH AZURE SMTP  *************************** */
    $app->post('/oauth/azure-test', function (Request $request, Response $response) use ($app) {
        // 1. Validar API-KEY
        $apiKeyPlana = $request->getHeaderLine('X-API-KEY');

        if (!$apiKeyPlana) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "API Key requerida en header X-API-KEY"
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $pdo = $app->getContainer()->get(PDO::class);
        $keys = $pdo->query("SELECT api_key FROM api_keys WHERE est = 1")
            ->fetchAll(PDO::FETCH_ASSOC);

        $valid = false;
        foreach ($keys as $row) {
            if (hash_equals(Openssl::get_ssl_decrypt($row['api_key']), $apiKeyPlana)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "API Key inválida o inactiva"
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        // 2. Obtener datos del body
        $data = $request->getParsedBody();

        $tenant_id = $data['tenant_id'] ?? '';
        $client_id = $data['client_id'] ?? '';
        $client_secret = $data['client_secret'] ?? '';

        if (!$tenant_id || !$client_id || !$client_secret) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Faltan parámetros: tenant_id, client_id, client_secret"
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // 3. Probar conexión con Azure
        $ch = curl_init("https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ]),
            CURLOPT_TIMEOUT => 10,
        ]);

        $raw = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Error de conexión con Azure",
                "detalle" => $err
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        if ($http_code !== 200) {
            $error = json_decode($raw, true);
            $response->getBody()->write(json_encode([
                "status" => "error",
                "message" => "Fallo autenticación con Azure",
                "http_code" => $http_code,
                "error_code" => $error['error'] ?? null,
                "detalle" => $error['error_description'] ?? 'Error desconocido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token_data = json_decode($raw, true);

        $response->getBody()->write(json_encode([
            "status" => "success",
            "message" => "Conexion exitosa con Azure",
            "access_token" => $token_data['access_token'],
            "expires_in" => $token_data['expires_in'],
            "token_type" => $token_data['token_type']
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    $app->post('/oauth/send-test-email', function (Request $request, Response $response) use ($app) {
        // Validar API-KEY
        $apiKeyPlana = $request->getHeaderLine('X-API-KEY');

        if (!$apiKeyPlana) {
            $response->getBody()->write(json_encode(["error" => "API Key requerida"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $pdo = $app->getContainer()->get(PDO::class);
        $keys = $pdo->query("SELECT api_key FROM api_keys WHERE est = 1")
            ->fetchAll(PDO::FETCH_ASSOC);

        $valid = false;
        foreach ($keys as $row) {
            if (hash_equals(Openssl::get_ssl_decrypt($row['api_key']), $apiKeyPlana)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $response->getBody()->write(json_encode(["error" => "API Key inválida"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        // Obtener credenciales del body
        $data = $request->getParsedBody();
        $tenant_id = $data['tenant_id'] ?? '';
        $client_id = $data['client_id'] ?? '';
        $client_secret = $data['client_secret'] ?? '';

        if (!$tenant_id || !$client_id || !$client_secret) {
            $response->getBody()->write(json_encode(["error" => "Faltan: tenant_id, client_id, client_secret"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Obtener token de Azure
        $ch = curl_init("https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ]),
            CURLOPT_TIMEOUT => 10,
        ]);

        $raw = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            $error = json_decode($raw, true);
            $response->getBody()->write(json_encode([
                "error" => "Fallo OAuth Azure",
                "detalle" => $error['error_description'] ?? 'Error desconocido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token_data = json_decode($raw, true);
        $access_token = $token_data['access_token'];

        // Enviar correo con PHPMailer + XOAuth2
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            // Configurar XOAuth2
            $mail->Username = 'noreply-informes@ubiquo.com';
            $auth_string = base64_encode('user=' . $mail->Username . "\x01auth=Bearer " . $access_token . "\x01\x01");
            $mail->Password = $auth_string;
            $mail->AuthType = 'XOAUTH2';

            $mail->setFrom('noreply-informes@ubiquo.com', 'Servicios Personal Tech');
            $mail->addAddress('mssp-calidad@personal.com.ar');
            $mail->addCC('mrgonzalez@personal.com.ar');

            $mail->isHTML(true);
            $mail->Subject = 'Test OAuth SMTP - Prueba de Integración';
            $mail->Body = '<h2>Prueba de envío con OAuth</h2>
                       <p>Este correo fue enviado exitosamente usando autenticación OAuth con Azure.</p>
                       <p><strong>Fecha:</strong> ' . date('Y-m-d H:i:s') . '</p>';

            $mail->send();

            $response->getBody()->write(json_encode([
                "status" => "success",
                "message" => "Correo enviado exitosamente",
                "to" => "mssp-calidad@personal.com.ar",
                "cc" => "mrgonzalez@personal.com.ar"
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                "error" => "Error al enviar correo",
                "detalle" => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
    //********************************** OAUTH AZURE SMTP FIN ***************************** */
};
