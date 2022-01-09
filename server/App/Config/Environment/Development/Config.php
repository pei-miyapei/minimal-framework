<?php

declare(strict_types=1);

namespace App\Config\Environment\Development;

final class Config
{
    /**
     * PHPコマンドのパス
     *
     * @var string
     */
    public const PhpCommandPath = '/usr/local/bin/php';

    /**
     * ログ保持期間（ファイルログ、〇〇_api_logの削除に使用）
     *
     * @var int
     */
    public const RetentionDays = 20;

    /**
     * エラーメール送信先
     *
     * @var string
     */
    public const ErrorMailTo = '';

    /**
     * メール送信元
     *
     * @var string
     */
    public const MailFrom = '';

    /**
     * ファイルログの有効化
     *
     * @var string
     */
    public const FileLogEnable = true;

    /**
     * ファイルログ出力先
     *
     * @var string
     */
    public const ServerPathToFileLogDirectory = '/workspace/server/storage/logs';

    /**
     * SQL実行ログの有効化
     * ※ エラー時のみ記録（lastInsertIdなどに影響するため）
     *
     * @var string
     */
    public const UseSqlLog = true;

    public static function isDebug(): bool
    {
        // switch (RequestControllerPath)
        // {
        //     case "/js/app":
        //         return false;
        // }

        return true;
    }
}
