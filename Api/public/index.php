<?php

declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// -----------------------------------------------------------------------------
// 1️⃣ CONFIGURAR CONTENEDOR
// -----------------------------------------------------------------------------
$containerBuilder = new ContainerBuilder();

if (false) { // ⚠️ cambiar a true en producción
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// -----------------------------------------------------------------------------
// 2️⃣ CONFIGURAR AJUSTES, DEPENDENCIAS Y REPOSITORIOS
// -----------------------------------------------------------------------------
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

// -----------------------------------------------------------------------------
// 3️⃣ CREAR APP Y CONTENEDOR
// -----------------------------------------------------------------------------
$container = $containerBuilder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();

// ⚙️ MUY IMPORTANTE: definir base path (por estar dentro de /tasking_stg/Api/public)
$app->setBasePath('/tasking_stg/Api');

$callableResolver = $app->getCallableResolver();

// -----------------------------------------------------------------------------
// 4️⃣ MIDDLEWARE
// -----------------------------------------------------------------------------
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// -----------------------------------------------------------------------------
// 5️⃣ RUTAS
// -----------------------------------------------------------------------------
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

// -----------------------------------------------------------------------------
// 6️⃣ CONFIGURAR ERROR HANDLER
// -----------------------------------------------------------------------------
/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);
$displayErrorDetails = $settings->get('displayErrorDetails');
$logError = $settings->get('logError');
$logErrorDetails = $settings->get('logErrorDetails');

// Crear Request desde entorno global
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Crear manejadores de error y apagado
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// -----------------------------------------------------------------------------
// 7️⃣ MIDDLEWARES DE RUTEO Y ERRORES
// -----------------------------------------------------------------------------
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(
    $displayErrorDetails,
    $logError,
    $logErrorDetails
);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// -----------------------------------------------------------------------------
// 8️⃣ EJECUTAR APLICACIÓN
// -----------------------------------------------------------------------------
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
