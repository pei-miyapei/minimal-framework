<?php

declare(strict_types=1);

namespace App\Config;

use App\Config\Exception\NotNotifiedException;
use App\Entity\Service\FileLogService;
use Dependency\Dependency;
use SmallFramework\Core\Config\ConfigService;
//use SmallFramework\Core\Entity\Model\ThrowableLogModel;
//use SmallFramework\Core\Entity\Service\CommandExecutionService;
//use SmallFramework\Core\Entity\Service\ErrorHandlerService;
use SmallFramework\Core\Entity\Model\ThrowableLogModel;
use SmallFramework\Core\Entity\Service\CommandExecutionService;
use SmallFramework\Core\Entity\Service\ExceptionHandlerService as CoreExceptionHandlerService;
use SmallFramework\Core\Entity\Service\MailService;

final class ExceptionHandlerService extends CoreExceptionHandlerService
{
    public static function exceptionHandler(\Throwable $throwable): void
    {
        // ログへの記録を試みる
        self::addExceptionLogForAppDefault($throwable);

        // メール送信を試みる
        // Dependency::callMethod(__CLASS__, 'sendMailForAppDefault', [$throwable]);

        // Viewによる出力を試みる
        parent::exceptionHandler($throwable);
    }

    /** ログ（テーブルやファイル）への記録 */
    public static function addExceptionLogForAppDefault(\Throwable $throwable): void
    {
        try {
            if (
                   RequestControllerPath === '/cli/log'
                //|| php_sapi_name() === "cli"
            ) {
                // ※ ログ用コントローラによる実行の場合は処理しない
                return;
            }

            if (ConfigService::getConstant('FileLogEnable') === true) {
                $fileNameSuffix = str_replace('/', '-', RequestControllerPath).'-error';

                FileLogService::add("例外を検知\n\n".(string) $throwable, $fileNameSuffix);
            }

            // 例外ログモデル
            $throwableLogModel = new ThrowableLogModel();
            $throwableLogModel->initialize($throwable);

            // 書き込みはコマンドライン経由で非同期実行する
            CommandExecutionService::execute('/cli/log?a=throwableLog', $throwableLogModel);
        } catch (\Throwable $dummy) {
            // 失敗時は何もしない
        }
    }

    /** メール通知 */
    public static function sendMailForAppDefault(\Throwable $throwable): void
    {
        try {
            if ($throwable instanceof NotNotifiedException) {
                return;
            }

            // 件名
            $subject = '例外を検知しました';

            // エラー送信先
            $emailAddress = ConfigService::getConstant('ErrorMailTo');

            // 送信
            MailService::send(
                $emailAddress,
                $subject,
                (string) $throwable,
                ConfigService::getConstant('MailFrom')
            );
        } catch (\Throwable $dummy) {
            // 失敗時は何もしない
        }
    }
}
