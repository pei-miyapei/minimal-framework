<?php

declare(strict_types=1);

namespace SmallFramework\Core\Gateway\DataAccess;

use SmallFramework\Core\Entity\Model\ThrowableLogModel;

class ThrowableLogRepository extends DatabaseRepository
{
    protected string $tableName = 'throwable_logs';

    /** INSERT を実行する */
    public function insert(ThrowableLogModel $throwableLogModel): \PDOStatement
    {
        // 新規登録用カラム
        $columns = $throwableLogModel->getCollectionForInsert();

        $sql = '
            INSERT INTO '.addslashes($this->tableName).'
                ('.implode(', ', array_keys($columns)).')
            VALUES
                ('.implode(', ', $columns).')
        ';

        return $this->pdo->query($sql);
    }

    /** 日付より古いものを削除 */
    public function deleteOlderThanDate(string $date): \PDOStatement
    {
        $sql = '
            DELETE FROM '.addslashes($this->tableName)."
            WHERE
                created_at < '".addslashes($date)."'
        ";

        return $this->pdo->query($sql);
    }
}
