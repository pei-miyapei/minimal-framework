<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config;

use Dependency\Dependency;
use SmallFramework\Core\Controller\DefaultController;
use SmallFramework\Core\Debug;
use SmallFramework\Core\Entity\Service\ErrorHandlerService;
use SmallFramework\Core\Entity\Service\ExceptionHandlerService;
use SmallFramework\Core\Entity\Service\ShutdownHandlerService;

class BasicPreferenceService implements BasicPreferenceServiceInterface
{
    /**
     * デフォルトで起動するコントローラーを変更したいときに指定
     *
     * @var string
     */
    protected $defaultController = DefaultController::class;

    public function __construct()
    {
        Dependency::singleton($this);

        // 固定
        // 念の為
        ini_set('session.auto_start', '0');

        // 文字コード
        mb_language('uni');
        mb_internal_encoding('UTF-8');
        header('Content-Type: text/html; charset=UTF-8');

        // タイムゾーン
        date_default_timezone_set('Asia/Tokyo');

        // ロケール
        setlocale(LC_TIME, 'ja_JP.UTF8');

        // キャッシュ対策
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

        // キャッシュ対策（HTTP/1.1）
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);

        // キャッシュ対策（HTTP/1.0）
        header('Pragma: no-cache');

        // 和暦・曜日等の表示用
        setlocale(LC_TIME, 'ja_JP.utf8', 'Japanese_Japan.932');

        // 定数：Configディレクトリの名前空間
        if (\defined('BaseServerPath') && \defined('ServerPathToConfigDirectory')) {
            $namespaceToConfigDirectory = str_replace(BaseServerPath, '', ServerPathToConfigDirectory);
            $namespaceToConfigDirectory = str_replace('/', '\\', $namespaceToConfigDirectory);
            \define('NamespaceToConfigDirectory', $namespaceToConfigDirectory);
        }

        // 環境により変更
        $this->setBasicPreferences();
    }

    /**
     * デフォルトで起動するコントローラー名を設定する
     *
     * @param string $controllerClassName コントローラーの完全修飾クラス名
     */
    public function setDefaultController(string $controllerClassName): void
    {
        if (empty($this->defaultController) || !class_exists($this->defaultController)) {
            return;
        }

        $this->defaultController = $controllerClassName;
    }

    /**
     * デフォルトで起動するコントローラー名を返す
     * （コントローラーが見つからない場合などに使用）
     */
    public function getDefaultControllerClassName(): string
    {
        return $this->defaultController;
    }

    /**
     * 基本環境設定
     * まとめただけ
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
    }
}
