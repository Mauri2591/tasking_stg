<?php
require_once __DIR__ . "/../../Config/Conexion.php";
require_once __DIR__ . "/../../Config/Config.php";
session_destroy();
header("Location:" . URL);
