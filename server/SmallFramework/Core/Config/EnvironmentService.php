<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config;

use SmallFramework\Core\Config\Enum\Environment;
use SmallFramework\Core\Dependency;

/**
 * 環境サービス
 *
 * 環境判別＆環境別定数サービスクラス読み込み
 */
class EnvironmentService implements EnvironmentServiceInterface
{
    /**
     * 実行環境
     *
     * @var Environment
     */
    protected $environment;

    /** 環境判別初期化処理 */
    public function __construct()
    {
        Dependency::singleton($this);

        // 環境ごとのパスの違いを利用して __DIR__ で環境判別する場合
        $this->environment = $this->getEnvironmentByServerPath();
    }

    /**
     * 環境判別用のEnvironmentインスタンスを返す
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * 開発環境かどうかを返す
     * $this->getEnvironment()->equals( enum ) のラッパー
     */
    public function isDevelopment(): bool
    {
        return $this->getEnvironment()->equals(Environment::Development());
    }

    /**
     * 本番環境かどうかを返す
     * $this->getEnvironment()->equals( enum ) のラッパー
     */
    public function isProduction(): bool
    {
        return $this->getEnvironment()->equals(Environment::Production());
    }

    /**
     * 環境判別
     *
     * 環境ごとの、configディレクトリまでのサーバ内部パスの違いによって
     * 対応する環境の値（Environment）を返却する
     *
     * （パスが全く同じ場合は使用できない）
     */
    protected function getEnvironmentByServerPath(): Environment
    {
        // 開発
        return Environment::Development();
    }
}
