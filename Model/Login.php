<?php
require_once __DIR__ . "/../Config/Config.php";

class Login extends Conexion
{
    public function set_login($usu_correo, $usu_pass)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tm_usuario.usu_id AS usu_id, tm_usuario.usu_pass AS usu_pass, tm_usuario.usu_nom AS usu_nom, 
        tm_usuario.usu_correo,tm_usuario.rol_id AS rol_id,tm_usuario.est, 
        sectores.sector_id AS sector_id,sectores.sector_nombre AS sector_nombre FROM tm_usuario 
        INNER JOIN sectores ON tm_usuario.sector_id=sectores.sector_id 
        WHERE usu_correo=? AND tm_usuario.est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($usu_correo, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->execute();
        $resul = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resul == false) {
            header("Location:" . URL . "?err=err_usu");
        } else if (!password_verify($usu_pass, $resul['usu_pass'])) {
            header("Location:" . URL . "?err=err_pass");
            exit();
        } else {
            session_regenerate_id();
            $_SESSION['usu_id'] = $resul['usu_id'];
            $_SESSION['usu_nom'] = $resul['usu_nom'];
            $_SESSION['rol_id'] = $resul['rol_id'];
            $_SESSION['sector_id'] = $resul['sector_id'];
            $_SESSION['sector_nombre'] = $resul['sector_nombre'];
            $_SESSION['bienvenido'] = "Bienvenido " . $resul['usu_nom'];
            header("Location:" . URL . "View/Home/");
            exit;
        }
    }
}
