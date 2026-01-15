<?php
declare(strict_types=1);

use Slim\App;
use App\Middleware\SessionMiddleware;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(SessionMiddleware::class);
};
