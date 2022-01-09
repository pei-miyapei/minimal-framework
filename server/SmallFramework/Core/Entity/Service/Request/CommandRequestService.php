<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service\Request;

use SmallFramework\Core\Dependency;

final class CommandRequestService extends RequestService
{
    /** $GLOBALS["argv"] */
    private array $argv = [];

    public function __construct()
    {
        Dependency::singleton($this);

        // コマンドライン実行時の引数
        if (isset($GLOBALS['argv'])) {
            $this->argv = $GLOBALS['argv'];
        }
    }

    /**
     * $GLOBALS["argv"] の値を取得
     *
     * @param int|string $key
     *
     * @return mixed
     */
    public function getArgv($key)
    {
        return !isset($this->argv[$key]) ? null : $this->argv[$key];
    }
}
