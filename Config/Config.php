<?php

use Dotenv\Dotenv;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Model/Clases/Openssl.php";

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

define("URL", $_ENV['URL']);
define("KEY", $_ENV['KEY']);
define("BASE_URL", getenv('URL'));
define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/tasking_stg/");
