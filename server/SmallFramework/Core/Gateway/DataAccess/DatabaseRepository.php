<?php

declare(strict_types=1);

namespace SmallFramework\Core\Gateway\DataAccess;

abstract class DatabaseRepository
{
    /**
     * fetch時のデフォルトのモデルクラスを1件指定することができる
     *
     * PDOのフェッチモードがPDO::FETCH_CLASSまたはPDO::FETCH_INTOの場合に
     * このファイル内の検索系メソッドを使用した場合、
     * レスポンスのPDOStatementにあらかじめクラスをセットしてから返す
     */
    protected string $modelClassName = '';

    /**
     * テーブル名
     */
    protected string $tableName = '';

    public function __construct(
        protected \PDO $pdo
    ) {
    }

    /**
     * 現在のPDOのフェッチモードに応じて $this->ModelClassName のクラスをセットする
     * PDO::FETCH_INTO、PDO::FETCH_CLASS（+ PDO::FETCH_PROPS_LATE）に対応
     */
    public function setFetchClass(\PDOStatement $pdoStatement, string $modelClassName = ''): void
    {
        if (empty($modelClassName) && !empty($this->modelClassName)) {
            $modelClassName = $this->modelClassName;
        }

        if (empty($modelClassName)) {
            return;
        }

        $fetchMode = $this->pdo->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE);

        if (\in_array($fetchMode, [\PDO::FETCH_CLASS, \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE], true)) {
            $pdoStatement->setFetchMode(
                $fetchMode,
                $modelClassName
            );
        } elseif ($fetchMode === \PDO::FETCH_INTO) {
            $pdoStatement->setFetchMode(
                $fetchMode,
                new $modelClassName()
            );
        }
    }

    /**
     * fetch時のデフォルトのモデルとして設定されているクラスのインスタンスを返す
     * 未設定時は stdClass を返す
     */
    public function getNewModel(): object
    {
        return empty($this->modelClassName) || !class_exists($this->modelClassName)
            ? new \stdClass()
            : new $this->modelClassName()
        ;
    }
}
