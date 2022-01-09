<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config;

use SmallFramework\Core\Config\Enum\Environment;

interface DatabaseServiceInterface
{
    /**
     * /Path/To/Config/Environment/(環境) ディレクトリの中の
     * PdoConnection(n).php ファイルから、
     * singletonインスタンスを生成・登録
     * ※ 変更不要
     */
    public function __construct(Environment $environment);
}
