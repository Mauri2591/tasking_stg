<?php
class Usuarios extends Conexion
{
    public function get_usuarios()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT usu_id,usu_nom,usu_correo,usu_tel,sectores.sector_nombre 
        AS sector FROM tm_usuario INNER JOIN sectores 
        ON tm_usuario.sector_id=sectores.sector_id 
        WHERE tm_usuario.est= 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sectores()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT sector_id,sector_nombre FROM sectores WHERE sector_id != 5 AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editarPerfil($usu_id, $usu_pass)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE tm_usuario SET usu_pass=:usu_pass WHERE usu_id=:usu_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":usu_pass", password_hash($usu_pass, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(":usu_id", $usu_id, PDO::PARAM_STR);
        $stmt->execute();
    }

	public function insert_usuario($usu_crea, $usu_nom, $usu_correo, $usu_pass, $usu_tel, $sector_id, $rol_id)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO tm_usuario (usu_crea,usu_nom, usu_correo,usu_pass,usu_tel,sector_id, rol_id) VALUES (:usu_crea,:usu_nom,:usu_correo, :usu_pass, :usu_tel,:sector_id, :rol_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':usu_crea', $usu_crea, PDO::PARAM_INT);
        $stmt->bindValue(':usu_nom', htmlspecialchars($usu_nom, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(':usu_correo', htmlspecialchars($usu_correo, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(':usu_pass', htmlspecialchars($usu_pass, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(':usu_tel', htmlspecialchars($usu_tel, ENT_QUOTES),  PDO::PARAM_STR);
        $stmt->bindValue(':sector_id', htmlspecialchars($sector_id, ENT_QUOTES),  PDO::PARAM_STR);
        $stmt->bindValue(':rol_id', $rol_id,  PDO::PARAM_INT);
        $stmt->execute();
   	 }
}
