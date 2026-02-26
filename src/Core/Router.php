<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\HomeController;

final class  Router
{
    public function routes(): void
    {
        (new HomeController())->index();
    }
}
