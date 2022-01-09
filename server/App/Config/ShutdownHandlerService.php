<?php

declare(strict_types=1);

namespace App\Config;

use SmallFramework\Core\Entity\Service\ShutdownHandlerService as CoreShutdownHandlerService;

final class ShutdownHandlerService extends CoreShutdownHandlerService
{
    public static function shutdownHandler(): void
    {
        $error = error_get_last();

        if (empty($error)) {
            // exitでもシャットダウンハンドラは呼ばれるためこのチェックは必須
            return;
        }

        // 例外時と同様の処理を試みる
        ExceptionHandlerService::exceptionHandler(
            new \Exception("シャットダウンハンドラ\n致命的なエラーを検知しました\n".var_export($error, true))
        );
    }
}
