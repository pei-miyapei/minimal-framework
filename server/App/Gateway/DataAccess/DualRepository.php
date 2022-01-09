<?php

declare(strict_types=1);

namespace App\Gateway\DataAccess;

use App\Config\Environment\Connection1Repository;

class DualRepository extends Connection1Repository
{
    /**
     * @param string $key
     *
     * @return bool
     */
    public function getLock($key)
    {
        $sql = "
            SELECT
                GET_LOCK('".addslashes($key)."', 1) AS result
        ";
        $pdoStatement = $this->pdo->query($sql);
        $row = $pdoStatement->fetch();

        return ($row->result ?? 0) === 1;
    }
}
