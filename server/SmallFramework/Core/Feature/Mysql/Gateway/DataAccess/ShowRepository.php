<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Mysql\Gateway\DataAccess;

use SmallFramework\Core\Feature\Mysql\Entity\Collection\Show\FullColumnsCollection;
use SmallFramework\Core\Gateway\DataAccess\DatabaseRepository;

class ShowRepository extends DatabaseRepository
{
    /** CREATE文を取得する */
    public function getCreateStatement(string $tableName): string
    {
        $pdoStatement = $this->pdo->query(sprintf(
            'SHOW CREATE TABLE `%s`',
            addslashes($tableName)
        ));
        $row = $pdoStatement->fetch();

        return $row->{'Create Table'} ?? '';
    }

    /**
     * SHOW FULL COLUMNS
     * カラム情報を取得
     */
    public function showFullColumns(string $tableName): FullColumnsCollection
    {
        $pdoStatement = $this->pdo->query(sprintf(
            'SHOW FULL COLUMNS FROM `%s`',
            addslashes($tableName)
        ));
        $this->setFetchClass($pdoStatement, '\\SmallFramework\\Core\\Feature\\Mysql\\Entity\\Model\\Show\\FullColumnsModel');

        return new FullColumnsCollection($pdoStatement->fetchAll());
    }

    /**
     * SHOW TABLE STATUS
     * テーブル情報を取得
     */
    public function showTableStatus(string $tableName): object
    {
        $pdoStatement = $this->pdo->query(sprintf(
            "SHOW TABLE STATUS WHERE Name = '%s'",
            addslashes($tableName)
        ));

        return !$pdoStatement ? new \stdClass() : $pdoStatement->fetch();
    }
}
