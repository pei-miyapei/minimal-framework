<?php

declare(strict_types=1);

namespace SmallFramework\Core;

use SmallFramework\Core\Entity\Service\ApplicationService;
use SmallFramework\Core\Entity\Service\Request\CommandRequestService;
use SmallFramework\Core\Entity\Service\Request\RequestService;
use SmallFramework\Core\Entity\Service\Request\RequestServiceInterface;
use SmallFramework\Core\Entity\Service\RoutingRequestParsingService;
use SmallFramework\Core\Entity\Service\SessionService;
use SmallFramework\Core\View\View;

/**
 * フロントコントローラー
 *
 * 処理の実行基点
 */
class FrontController
{
    /**
     * コンストラクター
     * newされた時点で実行開始します
     */
    public function __construct(?string $overrideControllerPath = null)
    {
        // デバッグ用タイマー
        Timer::initialize();

        // セッション開始
        (new SessionService())->start();

        // ルーティングリクエストの解析
        /*
            プロジェクトの基点までのURLパス（baseUrlPath）と、
            実行するコントローラのパス（controllerPath）をリクエスト内容から判定する
        */
        $routingRequestParsingService = new RoutingRequestParsingService(
            $this->initializeRequestService() // リクエストモデル
        );

        // 定数：プロジェクトの基点までのURLパス
        \define('BaseUrlPath', $routingRequestParsingService->baseUrlPath);
        // 定数：リクエストされたコントローラのパス
        \define('RequestControllerPath', $overrideControllerPath ?? $routingRequestParsingService->controllerPath);
        // 定数：リクエストされたアクション
        \define('RequestAction', $routingRequestParsingService->action);
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        if (Debug::isDebug() && RequestControllerPath !== '/command/log' && \PHP_SAPI !== 'cli') {
            // デバッグモードのみ。ログ処理、CLIの場合もスルー
            // タイマー
            Timer::push(__CLASS__.'::'.__FUNCTION__);

            $temp = Timer::getResult();
            Debug::trace($temp);

            // 描画
            echo Debug::getTraceHtml();
        }
    }

    /** アプリケーションに処理を移譲 */
    public function dispatch(): void
    {
        /*
            ▼ 正常系
            ・アプリケーションサービス
                ・（アクションが存在しない場合はnotFoundアクションに）
                ・authenticationメソッド
                ・authorizationメソッド
                ・アクションの実行
            ・FrontControllerによる出力

            ▼ コントローラー内例外の例外処理
            ・アプリケーションサービス
            ・（コントローラの処理内で例外発生）
            ・exceptionアクションを実行（※ オーバーライドで挙動を変更可能）
                ・errorアクションを実行
                ・例外を再スローし、コントローラー外例外の例外処理に乗せる
                　（仮にオーバーライドでスローしないようにした場合、FrontControllerによる出力に。）

            ▼ コントローラー外例外の例外処理
            ・（キャッチされない例外の発生）
            ・例外ハンドラによる出力

            ▼ シャットダウン
            ・（シャットダウン発生）
            ・シャットダウンハンドラによる出力
        */

        // View 初期化
        $view = Dependency::call(View::class);

        Timer::push('アプリケーション実行準備直前');

        // アプリケーション実行
        Dependency::call(ApplicationService::class);

        // 出力内容取得
        $output = $view->render();

        if ($output !== '') {
            // 出力内容があれば出力
            echo $output;
        }
    }

    /**
     * リクエストモデルの初期化を行う
     *
     * 通常は RequestService が使用し、
     * コマンドラインの場合は CommandRequestService を使用する
     */
    private function initializeRequestService(): object
    {
        $className = \PHP_SAPI !== 'cli'
            ? RequestService::class
            : CommandRequestService::class
        ;

        // インターフェースに関連付け
        Dependency::bind(RequestServiceInterface::class, $className);

        return new $className();
    }
}
