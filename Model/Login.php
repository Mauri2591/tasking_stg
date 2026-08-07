<?php
require_once __DIR__ . "/../Config/Config.php";
class Login extends Conexion
{
    private $excluir=[104, 112]; //Es para escluirme los usuarios de test del audit_login 
    public function audit_login($usu_id, $sector_id,$login)
    {   
        
        if(!isset($_SESSION['usu_id']) || in_array($_SESSION['usu_id'],$this->excluir)) return;        
        $conn = parent::get_conexion();
        $sql = "INSERT INTO audit_login (usu_id,sector_id,login) VALUES(:usu_id,:sector_id,:login)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':usu_id', $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function audit_logout($usu_id, $sector_id,$logout)
    {   
        if(!isset($_SESSION['usu_id']) || in_array($_SESSION['usu_id'],$this->excluir)) return;        
        $conn = parent::get_conexion();
        $sql = "INSERT INTO audit_login (usu_id,sector_id,logout) VALUES(:usu_id,:sector_id,:logout)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':usu_id', $usu_id, PDO::PARAM_INT);
        $stmt->bindValue(':sector_id', $sector_id, PDO::PARAM_INT);
        $stmt->bindValue(':logout', $logout, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function set_login($usu_correo, $usu_pass)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tm_usuario.usu_id AS usu_id, tm_usuario.usu_pass AS usu_pass, tm_usuario.usu_nom AS usu_nom, 
        tm_usuario.usu_correo,tm_usuario.rol_id AS rol_id,tm_usuario.lider, tm_usuario.est, 
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
            if (!empty($_SESSION['token_csrf'])) {
                if (hash_equals($_SESSION['token_csrf'], $_POST['token_csrf'])) {
                    session_regenerate_id();
                    $_SESSION['usu_id'] = $resul['usu_id'];
                    $_SESSION['usu_nom'] = $resul['usu_nom'];
                    $_SESSION['usu_correo'] = $resul['usu_correo'];
                    $_SESSION['rol_id'] = $resul['rol_id'];
                    $_SESSION['sector_id'] = $resul['sector_id'];
                    $_SESSION['sector_nombre'] = $resul['sector_nombre'];
                    $_SESSION['lider'] = $resul['lider'];
                    $_SESSION['bienvenido'] = "Bienvenido " . $resul['usu_nom'];
                    $this->audit_login($_SESSION['usu_id'], $_SESSION['sector_id'],"SI"); //Inserto en tabla audit el login
                    header("Location:" . URL . "View/Home/");
                    exit;
                } else {
                    header("Location:" . URL . "?err=err_csrf");
                    exit;
                }
            } else {
                header("Location:" . URL . "?err=err_csrf");
                exit;
            }
        }
    }
}
