<?php

declare(strict_types=1);


require __DIR__ . '/../vendor/autoload.php';


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



    /** LOGIN: Genera Access Token + Refresh Token */
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


    /** REFRESH: Genera nuevo Access Token usando Refresh Token*/
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

    /** LOGOUT: Revoca el Refresh Token */
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
            $row['hosts'] = json_decode($row['hosts'], true);

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
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : []
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
                $row['pais_nombre']
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
            $row['hosts'] = json_decode($row['hosts'], true);

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
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : []
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
                $row['pais_nombre']
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
        GROUP BY pg.id;";
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
            $row['hosts'] = json_decode($row['hosts'], true);

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
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : []
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
                $row['pais_nombre']

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
            $row['hosts'] = json_decode($row['hosts'], true);

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
                'nombres' => $row['nombres_usuarios_asignados'] ? explode(',', $row['nombres_usuarios_asignados']) : []
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
            );
        }
        $response->getBody()->write(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    });

    // Grupo protegido /users
    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    })->add(JwtMiddleware::class);

    // Grupo protegido /projects
    $app->group('/projects', function (Group $group) {
        $group->get('', ListProjectsAction::class);
        $group->get('/{id}', ViewProjectAction::class);
    })->add(JwtMiddleware::class);
};
