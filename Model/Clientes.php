<?php
class Clientes extends Conexion
{
    public function get_clientes()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM clientes WHERE est=1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $resul = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }

    public function get_paise_x_id(int $client_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT tm_pais.pais_id,tm_pais.pais_nombre,clientes.client_rs FROM tm_pais 
        INNER JOIN clientes ON tm_pais.pais_id=clientes.pais_id 
        WHERE clientes.client_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($client_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_cliente(string $client_rs, int $pais_id, string $client_cuit, string $client_correo, string $client_tel)
    {
        $conn = parent::get_conexion();
        $sql = "INSERT INTO clientes (client_rs, pais_id, client_cuit, client_correo, client_tel, est) VALUES (:client_rs, :pais_id, :client_cuit, :client_correo, :client_tel,1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":client_rs", htmlspecialchars($client_rs, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":pais_id", $pais_id, PDO::PARAM_INT);
        $stmt->bindValue(":client_cuit", htmlspecialchars($client_cuit, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":client_correo", htmlspecialchars($client_correo, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(":client_tel", htmlspecialchars($client_tel, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function get_datos_cliente(int $client_id)
    {
        $conn = parent::get_conexion();
        $sql = "SELECT * FROM clientes WHERE client_id=? AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($client_id, ENT_QUOTES), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function update_datos_cliente(string $client_id, string $client_rs, string $client_cuit, string $client_correo, string $client_tel)
    {
        $conn = parent::get_conexion();
        $sql = "UPDATE clientes SET client_rs=?, client_cuit=?, client_correo=?, client_tel=? WHERE client_id=? AND est=1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, htmlspecialchars($client_rs, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(2, htmlspecialchars($client_cuit, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(3, htmlspecialchars($client_correo, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(4, htmlspecialchars($client_tel, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->bindValue(5, htmlspecialchars($client_id, ENT_QUOTES), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function get_sectores()
    {
        $conn = parent::get_conexion();
        $sql = "SELECT sector_id,sector_nombre FROM sectores WHERE est=1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
