<?php

declare(strict_types=1);

namespace App\Config\Environment;

use SmallFramework\Core\Pdo;

abstract class PdoConnectionBase extends Pdo
{
    /**
     * コール可能な関数を指定できます。
     *
     * コールバック関数内でログ用途などにSQLを発行する場合、
     * ループ防止のためコールバックしないようになってはいますが
     * LAST_INSERT_ID()への影響などに十分注意してください
     *
     * @param string $sql
     * @param array  $errorInfo
     *
     * @var null|callable
     */
    public $callbackForSqlExecution;

    /**
     * コールバック内でSQLが発行される場合に
     * 再度コールバックが呼ばれないようにするためのフラグ
     *
     * @var bool
     */
    protected $disableCallback = false;

    /**
     * @var string
     */
    protected $currentCharset = 'utf8';

    /**
     * コールバックを実行する
     *
     * @param string $sql
     */
    public function callback($sql, array $errorInfo): void
    {
        if (\is_callable($this->callbackForSqlExecution) && !$this->disableCallback) {
            $this->disableCallback = true;
            \call_user_func($this->callbackForSqlExecution, $sql, $errorInfo);
            $this->disableCallback = false;
        }
    }

    public function setNames(?string $charset = null): void
    {
        if ($charset === null) {
            // nullの場合はutfに戻す
            $this->query("SET NAMES 'utf8'");
            $this->currentCharset = 'utf8';
        } else {
            // 指定があれば変更する
            $this->query(sprintf("SET NAMES '%s'", addslashes($charset)));
            $this->currentCharset = $charset;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @see PDO::query()
     */
    public function query($sql, $mode = self::ATTR_DEFAULT_FETCH_MODE, ...$fetch_mode_args): \PDOStatement | false
    {
        $throwable = null;

        try {
            $pdoStatement = parent::query($this->convertSql($sql));
        } catch (\Throwable $throwable) {
            // エラーが起きても一旦キャッチしてコールバックは流す
        }

        $this->callback($sql, $this->errorInfo());

        if (!empty($throwable)) {
            throw $throwable;
        }

        return $pdoStatement;
    }

    /**
     * {@inheritDoc}
     *
     * @see PDO::exec()
     */
    public function exec($sql): int | false
    {
        $throwable = null;

        try {
            $count = parent::exec($this->convertSql($sql));
        } catch (\Throwable $throwable) {
            // エラーが起きても一旦キャッチしてコールバックは流す
        }

        $this->callback($sql, $this->errorInfo());

        if (!empty($throwable)) {
            throw $throwable;
        }

        return $count;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    protected function convertSql($sql)
    {
        switch ($this->currentCharset) {
            case 'ujis':
                return mb_convert_encoding($sql, 'CP51932', 'UTF-8');

            case 'utf8':
                return $sql;

            default:
                return mb_convert_encoding($sql, $this->currentCharset, 'UTF-8');
        }
    }
}
