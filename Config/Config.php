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