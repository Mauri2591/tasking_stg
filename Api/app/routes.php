<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use App\Application\Actions\Project\ListProjectsAction;
use App\Application\Actions\Project\ViewProjectAction;

use App\Middleware\JwtMiddleware; // ✅ Importante
use Firebase\JWT\JWT;

return function (App $app) {

    // 🔑 Endpoint para autenticación
    $app->post('/login', function ($request, $response) {
        $data = $request->getParsedBody();

        $user = $data['usuario'] ?? '';
        $pass = $data['password'] ?? '';

        if ($user === 'admin' && $pass === '1234') {
            $payload = [
                'sub' => $user,
                'iat' => time(),
                'exp' => time() + 3600 // 1 hora
            ];
            $jwt = JWT::encode($payload, $_ENV['KEY'], 'HS256');

            $response->getBody()->write(json_encode(['token' => $jwt]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Credenciales inválidas']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    });

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    // 🔒 Grupo /users protegido con JWT
    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    })->add(JwtMiddleware::class); // <-- 🔥 se protege con JWT

    // Grupo /projects (público por ahora)
    $app->group('/projects', function (Group $group) {
        $group->get('', ListProjectsAction::class);
        $group->get('/{id}', ViewProjectAction::class);
    });
};
