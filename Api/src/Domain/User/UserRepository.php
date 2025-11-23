<?php

declare(strict_types=1);

namespace App\Domain\User;

use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function datosUsuario(string $usu_nom): ?array
    {
        $sql = "SELECT usu_id, usu_nom, usu_ape, usu_correo, usu_pass 
            FROM tm_usuario 
            WHERE usu_nom = :usu_nom AND usu_correo='dev@teco.com.ar' AND est = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['usu_nom' => $usu_nom]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}
