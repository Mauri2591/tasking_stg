<?php
require_once __DIR__ . "/../../Config/Conexion.php";
require_once __DIR__ . "/../../Config/Config.php";
require_once __DIR__."/../../Model/Login.php";
$login=new Login();
$login->audit_logout($_SESSION['usu_id'],$_SESSION['sector_id'],"SI");
session_destroy();
header("Location:" . URL);
exit;
