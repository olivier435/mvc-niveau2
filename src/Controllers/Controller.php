<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class Controller
{
    protected function render(string $view, array $params = []): void
    {
        extract($params);

        require dirname(__DIR__, 2) . '/Views/' . $view . '.php';
    }
}
