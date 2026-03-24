<?php

declare(strict_types=1);

namespace App\Entities;

final class DashboardStats
{
    private int $totalCreations;
    private int $creationsWithPicture;
    private int $creationsWithoutPicture;

    public function __construct(
        int $totalCreations,
        int $creationsWithPicture,
        int $creationsWithoutPicture
    ) {
        $this->totalCreations = $totalCreations;
        $this->creationsWithPicture = $creationsWithPicture;
        $this->creationsWithoutPicture = $creationsWithoutPicture;
    }

    public function getTotalCreations(): int
    {
        return $this->totalCreations;
    }

    public function getCreationsWithPicture(): int
    {
        return $this->creationsWithPicture;
    }

    public function getCreationsWithoutPicture(): int
    {
        return $this->creationsWithoutPicture;
    }
}
