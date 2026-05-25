<?php

use Dotenv\Dotenv;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

define("URL", $_ENV['URL']);
define("KEY", $_ENV['KEY']);
define("BASE_URL", getenv('URL'));
define("BASE_PATH", $_ENV['BASE_PATH']);
define('ZIP_PATH', $_ENV['ZIP_PATH']);
define('ZIP_URL', $_ENV['ZIP_URL']);

// Correo
define('SMTP_HOST',      $_ENV['SMTP_HOST']);
define('SMTP_USER',      $_ENV['SMTP_USER']);
define('SMTP_PASS',      $_ENV['SMTP_PASS']);
define('SMTP_PORT',      $_ENV['SMTP_PORT']);
define('SMTP_FROM',      $_ENV['SMTP_FROM']);
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME']);
define('MAIL_COPIA_SECTORES', $_ENV['MAIL_COPIA_SECTORES'] ?? '');