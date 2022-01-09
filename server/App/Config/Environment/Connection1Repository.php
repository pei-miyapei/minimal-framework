<?php

declare(strict_types=1);

namespace App\Config\Environment;

use SmallFramework\Core\Gateway\DataAccess\DatabaseRepository;

class Connection1Repository extends DatabaseRepository
{
    public function __construct(PdoConnection1Interface $pdo)
    {
        $this->pdo = $pdo;
    }
}
