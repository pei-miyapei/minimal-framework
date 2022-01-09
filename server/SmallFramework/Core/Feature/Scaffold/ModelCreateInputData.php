<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Scaffold;

class ModelCreateInputData
{
    public function __construct(
        private \PDO $pdo,
        private ?string $tableName = null
    ) {
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }
}
