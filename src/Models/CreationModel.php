<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Creation;
use PDO;

final class CreationModel extends Model
{
    /**
     * @return Creation[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM creation ORDER BY created_at DESC'
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(
            fn(array $row) => Creation::createAndHydrate($row),
            $rows
        );
    }

    public function find(int $id): ?Creation
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM creation WHERE id_creation = :id'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Creation::createAndHydrate($row) : null;
    }

    public function insert(Creation $creation): Creation
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO creation (title, description, picture, created_at) 
            VALUES (:title, :description, :picture, :created_at)'
        );

        $stmt->execute([
            'title' => $creation->getTitle(),
            'description' => $creation->getDescription(),
            'picture' => $creation->getPicture(),
            'created_at' => $creation->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]);

        $creation->setIdCreation((int)$this->pdo->lastInsertId());

        return $creation;
    }

    public function update(int $id, Creation $creation): ?Creation
    {
        $stmt = $this->pdo->prepare(
            'UPDATE creation 
            SET title = :title, 
                description = :description, 
                picture = :picture 
            WHERE id_creation = :id'
        );

        $stmt->execute([
            'title' => $creation->getTitle(),
            'description' => $creation->getDescription(),
            'picture' => $creation->getPicture(),
            'id' => $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM creation WHERE id_creation = :id'
        );

        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function findPagInated(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM creation ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $rows = $stmt->fetchAll();

        return array_map(
            fn(array $row) => Creation::createAndHydrate($row),
            $rows
        );
    }
    public function countAll(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM creation");
        return (int) $stmt->fetchColumn();
    }
}
