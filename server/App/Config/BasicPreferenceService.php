<?php

declare(strict_types=1);

namespace App\Config;

use SmallFramework\Core\Config\BasicPreferenceService as CoreBasicPreferenceService;
use SmallFramework\Core\Controller\DefaultController;
use SmallFramework\Core\Debug;
use SmallFramework\Core\Entity\Service\ErrorHandlerService;

final class BasicPreferenceService extends CoreBasicPreferenceService
{
    /**
     * デフォルトで起動するコントローラーを変更したいときに指定
     *
     * @var string
     */
    protected $defaultController = DefaultController::class;

    /**
     * 基本環境設定
     *
     * {@inheritDoc}
     *
     * @see \SmallFramework\Core\Config\BasicPreferenceService::setBasicPreferences()
     */
    protected function setBasicPreferences(): void
    {
        Debug::trace('実行日時：'.date('Y-m-d H:i:s'));

        if (!empty($_SERVER['SERVER_ADDR'])) {
            Debug::trace('Webサーバー：'.$_SERVER['SERVER_ADDR']);
        }

        // エラー表示
        ini_set('display_errors', '0');
        error_reporting(E_ALL);

        // エラーハンドラ
        ErrorHandlerService::setHandler();

        // シャットダウンハンドラ
        ShutdownHandlerService::setHandler();

        // 例外ハンドラ
        ExceptionHandlerService::setHandler();

        // 時間制限なし
        set_time_limit(0);
    }
}
