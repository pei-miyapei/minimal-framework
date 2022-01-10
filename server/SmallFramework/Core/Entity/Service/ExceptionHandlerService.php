<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

use Dependency\Dependency;

class ExceptionHandlerService
{
    /** デフォルトの例外ハンドラ */
    public static function exceptionHandler(\Throwable $throwable): void
    {
        try {
            // Viewによる出力を試みる
            $view = Dependency::call('\\SmallFramework\\Core\\View\\View');
            $output = $view->render();

            if ($output !== '') {
                // 出力内容があれば出力
                echo $output;
            }
        } catch (\Throwable $dummy) {
            // 失敗時は何もしない
        }
    }

    /** 例外ハンドラを設定 */
    public static function setHandler(?callable $handler = null): void
    {
        if (!isset($handler)) {
            $handler = '\\'.static::class.'::exceptionHandler';
        }

        set_exception_handler($handler);
    }

    /** 例外ハンドラをリストア（一つ前の状態に戻す） */
    public static function restoreHandler(): void
    {
        restore_exception_handler();
    }
}
