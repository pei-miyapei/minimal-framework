<?php

declare(strict_types=1);

namespace App\Controller;

use Dependency\Dependency;
use SmallFramework\Core\Config\ConfigService;
use SmallFramework\Core\Config\DatabaseServiceInterface;
use SmallFramework\Core\Config\EnvironmentServiceInterface;
use SmallFramework\Core\Controller\DefaultController;

/**
 * コントローラー共通の機能実装例
 *
 * @note
 * ・クラス名が Controller で終わらないクラスは
 * 　URLによって起動することはない
 * ・エラー時にDefaultControllerの代わりに使用する
 */
class ControllerBase extends DefaultController
{
    /**
     * コンストラクター
     * アプリケーションの各コントローラーで共通の初期化
     */
    public function __construct()
    {
        /**
         * DB設定ロード＆PDOインスタンス生成
         *
         * @note 親クラスで引数自動DIすると子も全て定義しないといけないため別で呼ぶ
         */
        $environmentService = Dependency::call(EnvironmentServiceInterface::class);
        Dependency::call(
            DatabaseServiceInterface::class,
            [$environmentService->getEnvironment()]
        );

        // PDOコネクション1取得
        $pdo = Dependency::call(NamespaceToConfigDirectory.'\\Environment\\PdoConnection1Interface');

        // SQLログ用コールバック
        if (ConfigService::getConstant('UseSqlLog') === true) {
            // @see ControllerBase::callbackForSqlExecution()
            $pdo->callbackForSqlExecution = [$this, 'callbackForSqlExecution'];
        }
    }

    /**
     * コントローラー内例外の例外処理
     *
     * {@inheritDoc}
     *
     * @see \SmallFramework\Core\Controller\DefaultController::exception()
     */
    public function exception(\Throwable $throwable): void
    {
        // コントローラ外例外の例外処理へ
        throw $throwable;
    }

    /**
     * SQL実行直後のコールバック
     *
     * @note
     *  エラー時も例外をキャッチしてコールバックを呼んでからスローし直しているので
     *  実行直前のコールバックはありません
     *
     * @param mixed $sqlInfo
     */
    public function callbackForSqlExecution($sqlInfo, array $errorInfo): void
    {
    }
}
