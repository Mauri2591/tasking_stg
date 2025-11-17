<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
};

use App\Middleware\JwtMiddleware;

return function (App $app) {
    // middlewares globales
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();

    // JWT middleware
    $app->add(JwtMiddleware::class);
};
