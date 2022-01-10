<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

use Dependency\Dependency;

class ShutdownHandlerService
{
    /**
     * シャットダウンハンドラはエラーや例外ハンドラと異なり、
     * 一度設定したものをリストアすることができない。
     * （常に追加になり、戻せない）
     *
     * そのため「コールバックを複数保持して末尾の1件を実行するシャットダウンハンドラ」をラッパーとして作成し
     * 疑似的に他のハンドラと同じ挙動にする
     *
     * @var ?array
     */
    private static $shutdownHandlerCollection;

    /**
     * コールバックを複数保持して末尾の1件を実行するシャットダウンハンドラ
     * （実際に登録されているシャットダウンハンドラはこの関数のみ）
     */
    public static function shutdownHandlerWrapper(): void
    {
        \call_user_func(end(self::$shutdownHandlerCollection));
    }

    /** デフォルトのシャットダウンハンドラ */
    public static function shutdownHandler(): void
    {
        try {
            $error = error_get_last();

            if (empty($error)) {
                // exitでもシャットダウンハンドラは呼ばれるためこのチェックは必須
                return;
            }

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

    /** シャットダウンハンドラを設定 */
    public static function setHandler(?callable $handler = null): void
    {
        if (!isset(self::$shutdownHandlerCollection)) {
            // 初期化されていない初回でのみ register_shutdown_function を実行する
            // ここで登録しているのは実際には「コールバックを複数保持して末尾の1件を実行するシャットダウンハンドラ」
            self::$shutdownHandlerCollection = [];
            register_shutdown_function('\\'.static::class.'::shutdownHandlerWrapper');
        }

        if (!isset($handler)) {
            $handler = '\\'.static::class.'::shutdownHandler';
        }

        self::$shutdownHandlerCollection[] = $handler;
    }

    /** シャットダウンハンドラをリストア（一つ前の状態に戻す） */
    public static function restoreHandler(): void
    {
        array_pop(self::$shutdownHandlerCollection);
    }
}
