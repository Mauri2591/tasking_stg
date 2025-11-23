<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Project\ListProjectsAction;
use App\Application\Actions\Project\ViewProjectAction;
use App\Middleware\JwtMiddleware;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Domain\User\UserRepository;

return function (App $app) {


    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('<strong>Bienvenido a la API de Tasking.</strong> Si necesita ayuda, escríbale a Mauricio R. González del equipo de Ethickal Hacking');
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
            // Access Token (expira en 1 hora)
            $payload = [
                'sub' => $user['usu_id'],
                'name' => $user['usu_nom'],
                'iat' => time(),
                'exp' => time() + 3600
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


    $app->get('/proyectosActivos', function (Request $request, Response $response) use ($app) {

        $container = $app->getContainer();
        $pdo = $container->get(PDO::class);

        $stmt = $pdo->prepare("SELECT 
            pg.id AS id_proyecto,
            prr.posicion_recurrencia AS recurrencia,
            IF(pr.id IS NOT NULL,'SI','NO') AS rechequeo,
            pr.posicion_recurrencia AS rechequeo,
            IF(pg.descripcion = '', NULL, pg.descripcion) AS descripcion_proyecto,
            IF(pg.fech_inicio = '', NULL, pg.fech_inicio) AS fecha_inicio,
            IF(pg.fech_fin = '', NULL, pg.fech_fin) AS fecha_fin,
            pg.fech_vantive,
            GROUP_CONCAT(DISTINCT up.usu_asignado) AS ids_usuarios_asignados,
            GROUP_CONCAT(DISTINCT tu.usu_nom) AS nombres_usuarios_asignados,
            pg.estados_id AS id_estado_proyecto,
            te.estados_nombre AS estado_proyecto,
            tc.cat_nom AS producto,
            d.hs_dimensionadas,
            GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'IP' THEN h.host END) AS ips,
            GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'URL' THEN h.host END) AS urls,
            GROUP_CONCAT(DISTINCT CASE WHEN h.tipo NOT IN ('IP','URL') THEN h.host END) AS otros
        FROM proyecto_gestionado pg
        LEFT JOIN usuario_proyecto up ON pg.id = up.id_proyecto_gestionado
        LEFT JOIN tm_usuario tu ON up.usu_asignado = tu.usu_id
        LEFT JOIN tm_estados te ON pg.estados_id = te.estados_id
        LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
        LEFT JOIN proyecto_rechequeo pr ON pg.id = pr.id_proyecto_gestionado
        LEFT JOIN proyecto_recurrencia prr ON pg.id = prr.id_proyecto_gestionado
        LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado
        LEFT JOIN hosts h ON pg.id = h.id_proyecto_gestionado
        WHERE pg.estados_id NOT IN(14,15,16,17)
        GROUP BY pg.id");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            $response->getBody()->write(json_encode(['error' => 'Sin datos']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($result));
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


    //Endpoint para todos los proyectos
    /*
SELECT 
    pg.id AS id_proyecto,
    prr.posicion_recurrencia,
    IF(pr.id IS NOT NULL,'SI','NO') AS rechequeo,
    pr.posicion_recurrencia,
    IF(pg.descripcion = '', NULL, pg.descripcion) AS descripcion_proyecto,
    IF(pg.fech_inicio = '', NULL, pg.fech_inicio) AS fecha_inicio,
    IF(pg.fech_fin = '', NULL, pg.fech_fin) AS fecha_fin,
    pg.fech_vantive,
    GROUP_CONCAT(DISTINCT up.usu_asignado) AS ids_usuarios_asignados,
    GROUP_CONCAT(DISTINCT tu.usu_nom) AS nombres_usuarios_asignados,
    pg.estados_id AS id_estado_proyecto,
    te.estados_nombre AS estado_proyecto,
    tc.cat_nom AS producto,
    d.hs_dimensionadas,
    GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'IP' THEN h.host END) AS ips,
    GROUP_CONCAT(DISTINCT CASE WHEN h.tipo = 'URL' THEN h.host END) AS urls,
    GROUP_CONCAT(DISTINCT CASE WHEN h.tipo NOT IN ('IP','URL') THEN h.host END) AS otros
FROM proyecto_gestionado pg
LEFT JOIN usuario_proyecto up ON pg.id = up.id_proyecto_gestionado
LEFT JOIN tm_usuario tu ON up.usu_asignado = tu.usu_id
LEFT JOIN tm_estados te ON pg.estados_id = te.estados_id
LEFT JOIN tm_categoria tc ON pg.cat_id = tc.cat_id
LEFT JOIN proyecto_rechequeo pr ON pg.id = pr.id_proyecto_gestionado
LEFT JOIN proyecto_recurrencia prr ON pg.id = prr.id_proyecto_gestionado
LEFT JOIN dimensionamiento d ON pg.id = d.id_proyecto_gestionado
LEFT JOIN hosts h ON pg.id = h.id_proyecto_gestionado
WHERE pg.estados_id NOT IN(14,15,16,17)
GROUP BY pg.id;
*/
};
