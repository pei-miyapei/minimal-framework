<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config;

use SmallFramework\Core\Config\Enum\Environment;

interface EnvironmentServiceInterface
{
    /** 環境判別用のEnvironmentインスタンスを返す */
    public function getEnvironment(): Environment;

    /** 開発環境かどうかを返す */
    public function isDevelopment(): bool;

    /** 本番環境かどうかを返す */
    public function isProduction(): bool;
}
