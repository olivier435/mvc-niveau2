<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Entity;
use DateTimeImmutable;

final class Creation extends Entity
{
    private ?int $idCreation = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?string $picture = null;
    private ?DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    /* =======================
    * Getters
    * ======================= */
    public function getIdCreation(): ?int
    {
        return $this->idCreation;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /* =======================
    * Setters
    * ======================= */
    public function setIdCreation(int $id): void
    {
        $this->idCreation = $id;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    public function setPicture(?string $picture): void 
    {
        $this->picture = $picture !== null && $picture !== '' 
            ? trim($picture) 
            : null;
    }

    public function setCreatedAt(DateTimeImmutable|string $d): void 
    {
        if (is_string($d)) {
            $d = new DateTimeImmutable($d);
        }
        $this->createdAt = $d;
    }
}
