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

    // 🔹 Devuelve todos los usuarios activos
    public function findAll(): array
    {
        $sql = "SELECT usu_id, usu_nom, usu_ape, usu_correo 
                FROM tm_usuario 
                WHERE est = 1";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Devuelve un usuario por ID
public function findById(int $id): ?array
{
    $sql = "SELECT usu_id, usu_nom, usu_ape, usu_correo 
            FROM tm_usuario 
            WHERE usu_id = :id AND est = 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

}
