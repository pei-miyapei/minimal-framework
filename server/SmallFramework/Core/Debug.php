<?php

declare(strict_types=1);

namespace SmallFramework\Core;

use SmallFramework\Core\Config\ConfigService;
use SmallFramework\Core\Entity\Collection\TraceCollection;

class Debug
{
    /**
     * トレース用コレクション
     *
     * @var TraceCollection
     */
    private static $traceCollection;

    /** デバッグモードか */
    public static function isDebug(): bool
    {
        if (!ConfigService::isCallable('isDebug')) {
            // 環境設定にisDebugメソッドが無い場合（通常モード）
            return false;
        }

        // 環境設定にisDebugメソッドがある場合は、その戻り値
        return ConfigService::callMethod('isDebug');
    }

    /**
     * 平時は「NOW()」を
     * デバッグモードかつ、$_GET["now"] に指定がある場合は
     * strtotime を経由して「'YYYY-MM-DD HH:II:SS'」を返す。
     *
     * ※ 指定は日付部分のみ。
     * 　 時間は現在時刻を使用
     */
    public static function getNowFuncTextForSql(): string
    {
        if (!self::isDebug() || empty($_GET['now'])) {
            return 'NOW()';
        }

        return sprintf(
            "'%s %s'",
            date('Y-m-d', strtotime($_GET['now'])),
            date('H:i:s')
        );
    }

    /**
     * 平時は「CURDATE()」を
     * デバッグモードかつ、$_GET["now"] に指定がある場合は
     * strtotime を経由して「'YYYY-MM-DD'」を返す。
     */
    public static function getCurdateFuncTextForSql(): string
    {
        if (!self::isDebug() || empty($_GET['now'])) {
            return 'CURDATE()';
        }

        return date("'Y-m-d'", strtotime($_GET['now']));
    }

    /**
     * トレース用のコメントを記録
     * キーの指定がある場合、そのキーごとのトレースも保持する
     *
     * @param mixed $comment
     * @param mixed $traceKey
     */
    public static function trace($comment, $traceKey = ''): void
    {
        self::getTraceCollection()->add(
            sprintf('<pre>%s</pre>', \is_string($comment) ? $comment : var_export($comment, true)),
            !\is_array($traceKey) ? $traceKey : implode("\n", $traceKey)
        );
    }

    /**
     * 引数の文字列もしくはvar_exportした内容をトレース用に記録
     *
     * @param mixed $data
     * @param mixed $traceKey
     */
    public static function traceWithDump($data, string $comment = '', $traceKey = ''): void
    {
        if (\is_object($data) && method_exists($data, 'makeHtmlForDebug')) {
            // デバッグ用変換が用意されている場合は実行
            self::getTraceCollection()->add(
                sprintf(
                    '%s<div>%s</div>',
                    !\strlen($comment) ? '' : sprintf("▼ %s\n", $comment),
                    $data->makeHtmlForDebug()
                ),
                !\is_array($traceKey) ? $traceKey : implode("\n", $traceKey)
            );
        } else {
            if (!\is_string($data)) {
                // 文字列にする
                $data = var_export($data, true);
            }

            self::trace(
                self::getPreviewHtml($data, $comment, 'php'),
                $traceKey
            );
        }
    }

    /**
     * json文字列をトレース用に記録
     *
     * @param mixed $traceKey
     */
    public static function traceWithJson(string $json, string $comment = '', $traceKey = ''): void
    {
        self::trace(
            self::getPreviewHtml($json, $comment, 'json'),
            $traceKey
        );
    }

    /**
     * SQL文字列をトレース用に記録
     * ※ SQLはデフォルトのキーが「"sql"」固定。
     */
    public static function traceWithSql(string $sql, string $comment = '', string $traceKey = 'sql'): void
    {
        // ちょっと形成
        $sql = preg_replace("/^\t\t/m", '', $sql)."\t;\n\n";
        self::trace(
            self::getPreviewHtml($sql, $comment, 'sql'),
            'sql'
        );
    }

    /** トレース用HTMLを取得 */
    public static function getTraceHtml(): string
    {
        // 参照しやすそうな順番に変えておく
        self::getTraceCollection()->sort();
        // "sql" は末尾に移動
        self::getTraceCollection()->moveToEnd('sql');

        ob_start();
        $traceCollection = self::getTraceCollection();

        require __DIR__.'/View/debug.html';

        return ob_get_clean();
    }

    /**
     * 保持しているトレース用コレクションを返す。
     * （なければ new して保持してから返す）
     */
    private static function getTraceCollection(): TraceCollection
    {
        if (!isset(self::$traceCollection)) {
            self::$traceCollection = new TraceCollection();
        }

        return self::$traceCollection;
    }

    /**
     * 引数の内容を highlight.js 向けのHTMLフォーマットにして返す
     */
    private static function getPreviewHtml(string $data, string $comment = '', string $type = ''): string
    {
        return sprintf(
            '%s<code class="%s">%s</code>',
            !\strlen($comment) ? '' : sprintf("▼ %s\n", $comment),
            $type,
            $data
        );
    }
}
