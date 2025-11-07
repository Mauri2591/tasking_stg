<?php
require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/../Config/Config.php";
require_once __DIR__ . "/../Model/Login.php";
if (isset($_SERVER) && $_SERVER['REQUEST_METHOD'] === "POST") {
    $login = new Login();
    $login->set_login($_POST['usu_correo'], $_POST['usu_pass']);
}
