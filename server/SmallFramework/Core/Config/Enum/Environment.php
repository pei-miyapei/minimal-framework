<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config\Enum;

use SmallFramework\Core\Entity\Enum\Enum;

class Environment extends Enum
{
    /**
     * 本番環境
     *
     * @var string
     */
    public const Production = 'production';

    /**
     * テスト環境
     *
     * @var string
     */
    public const Test = 'test';

    /**
     * 開発環境
     *
     * @var string
     */
    public const Development = 'development';
}
