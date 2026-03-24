<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Creation;
use App\Entities\DashboardStats;
use PDO;

final class DashboardModel extends Model
{
    public function getCreationStats(): DashboardStats
    {
        $sql = <<<SQL
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN picture IS NOT NULL AND picture <> '' THEN 1 ELSE 0 END) AS with_picture,
            SUM(CASE WHEN picture IS NULL OR picture = '' THEN 1 ELSE 0 END) AS without_picture
        FROM creation
        SQL;

        $stmt = $this->pdo->query($sql);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        return new DashboardStats(
            (int) ($stats['total'] ?? 0),
            (int) ($stats['with_picture'] ?? 0),
            (int) ($stats['without_picture'] ?? 0)
        );
    }

    public function getLatestCreations(int $limit = 5): array
    {
        $limit = max(1, $limit);

        $sql = 'SELECT * FROM creation ORDER BY created_at DESC LIMIT :limit';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(
            fn(array $row) => Creation::createAndHydrate($row),
            $rows
        );
    }
}