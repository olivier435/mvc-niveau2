<?php

declare(strict_types=1);

namespace App\Entities;

abstract class Entity
{
    public static function createAndHydrate(array $data): static
    {
        $entity = new static();
        $entity->hydrate($data);
        return $entity;
    }
    protected function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (!method_exists($this, $method)) {
                continue;
            }
            $this->$method($value);
        }
    }
}
