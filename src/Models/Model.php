<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

abstract class Model
{
    protected PDO $pdo;
    public function __construct()

    {
        $this->pdo = DB::pdo();
    }
}
