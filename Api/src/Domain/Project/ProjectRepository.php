<?php
declare(strict_types=1);

namespace App\Domain\Project;

use PDO;

class ProjectRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // 🔹 Traer todos los proyectos
    public function findAll(): array
    {
        $sql = "SELECT *
                FROM proyecto_gestionado 
                WHERE est = 1";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Traer un proyecto por ID
    public function findById(int $id): ?array
    {
        $sql = "SELECT * 
                FROM proyecto_gestionado 
                WHERE id = :id AND est = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }
    
}
