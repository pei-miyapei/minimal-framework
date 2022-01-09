<?php

declare(strict_types=1);

namespace App\Feature\Demo\Gateway\DataAccess;

use App\Config\Environment\Connection1Repository;
use App\Feature\Demo\Entity\Model\DemoModel;
use SmallFramework\Core\PdoStatement;

class DemosQueryService extends Connection1Repository
{
    protected string $tableName = 'demos';
    protected string $modelClassName = DemoModel::class;

    public function fetchAll(): PdoStatement
    {
        $sql = '
            SELECT *
            FROM '.addslashes($this->tableName).'
        ';

        $pdoStatement = $this->pdo->query($sql);
        $this->setFetchClass($pdoStatement);

        return $pdoStatement;
    }
}
