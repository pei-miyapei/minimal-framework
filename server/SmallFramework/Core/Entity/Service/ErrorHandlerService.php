<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

final class ErrorHandlerService
{
    /**
     * エラーハンドラ
     * エラーを例外に変換する
     *
     * @throws \ErrorException
     */
    public static function errorHandler(int $errno, string $message, string $file, int $line): bool
    {
        // エラー通知設定のビットと実際に拾ったエラーのビット積で一致する（検知するもの）の否定
        // = 一致しないもの（スルーするもの）を出している
        if (!($errno & error_reporting())) {
            return true;
        }

        throw new \ErrorException($message, 0, $errno, $file, $line);
    }

    /** エラーハンドラを設定 */
    public static function setHandler(?callable $handler = null): void
    {
        if (!isset($handler)) {
            $handler = __CLASS__.'::errorHandler';
        }

        // エラーハンドラを設定
        set_error_handler($handler);
    }

    /** エラーハンドラをリストア（一つ前の状態に戻す） */
    public static function restoreHandler(): void
    {
        restore_error_handler();
    }
}
