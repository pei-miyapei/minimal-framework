<?php

declare(strict_types=1);

namespace SmallFramework\Core;

class Pdo extends \PDO
{
    /**
     * デバッグ用
     * データベース環境識別用の名称
     *
     * @var string
     */
    protected static $title = '';

    /**
     * ホスト名
     *
     * @var string
     */
    protected static $hostName = '';

    /**
     * ポート番号
     *
     * @var string
     */
    protected static $port = '3306';

    /**
     * データベース名
     *
     * @var string
     */
    protected static $database = '';

    /**
     * ユーザー名
     *
     * @var string
     *
     * @see https://github.com/dotnet/docs/issues/6523
     */
    protected static $username = '';

    /**
     * パスワード
     *
     * @var string
     */
    protected static $password = '';

    public function __construct(string $dsn, string $username = '', string $password = '', array $driverOption = [])
    {
        // エラーモード初期値
        if (!isset($driverOption[self::ATTR_ERRMODE])) {
            $driverOption[self::ATTR_ERRMODE] = self::ERRMODE_EXCEPTION;
        }

        // フェッチモード初期値
        if (!isset($driverOption[self::ATTR_DEFAULT_FETCH_MODE])) {
            $driverOption[self::ATTR_DEFAULT_FETCH_MODE] = self::FETCH_CLASS;
        }

        // PDOStatementクラスの初期値（拡張したクラスを使う）
        if (!isset($driverOption[self::ATTR_STATEMENT_CLASS])) {
            $driverOption[self::ATTR_STATEMENT_CLASS] = [PdoStatement::class, [$this]];
        }

        parent::__construct($dsn, $username, $password, $driverOption);
    }

    public function getTitle(): string
    {
        return static::$title;
    }

    public function getHostName(): string
    {
        return static::$hostName;
    }

    public function getDatabase(): string
    {
        return static::$database;
    }

    /**
     * @see https://github.com/dotnet/docs/issues/6523
     */
    public function getUsername(): string
    {
        return static::$username;
    }

    public static function getInstance(): ?self
    {
        try {
            $className = '\\'.static::class;
            $pdo = new $className(
                sprintf('mysql:host=%s;port=%s;dbname=%s', static::$hostName, static::$port, static::$database),
                static::$username,
                static::$password
            );

            // 後で参照できるよう、PDO自体に持たせておく
            // （ユーザ名、パスワードは危ないので入れない）
            $pdo::$hostName = static::$hostName;
            $pdo::$database = static::$database;
            $pdo::$title = static::$title;

            return $pdo;
        } catch (\Throwable $throwable) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @see \PDO::commit()
     */
    public function commit(): bool
    {
        Debug::trace('コミット');

        return parent::commit();
    }

    /**
     * {@inheritDoc}
     *
     * @see \PDO::rollBack()
     */
    public function rollBack(): bool
    {
        Debug::trace('ロールバック');

        return parent::rollBack();
    }
}
