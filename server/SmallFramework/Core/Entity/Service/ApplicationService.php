<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

use SmallFramework\Core\Config\BasicPreferenceServiceInterface;
use SmallFramework\Core\Controller\DefaultController;
use SmallFramework\Core\Dependency;

/**
 * リクエストされたコントローラとアクションを判定し、
 * アプリケーションを実行する
 */
final class ApplicationService
{
    /**
     * リクエストされたコントローラとアクションを判定して保持
     */
    public function __construct(BasicPreferenceServiceInterface $basicPreferenceService)
    {
        // デフォルトコントローラー
        $defaultControllerClassName = $basicPreferenceService->getDefaultControllerClassName();

        // リクエストされたコントローラーパスから
        // 実行するコントローラークラスを判定して取得
        $controllerClassName = $this->getControllerClassName($defaultControllerClassName);

        // リクエストされたアクション
        // 何も指定がない場合（空文字の場合）はindexが指定されたものとして扱う
        $action = null === RequestAction || \strlen(RequestAction) === 0 ? 'index' : (string) RequestAction;

        $controller = null;

        try {
            // コントローラーのインスタンスを準備
            $controller = Dependency::call($controllerClassName);

            if (!\is_callable([$controller, $action])) {
                // 指定されたアクションがコントローラーに存在しない場合、notFoundアクションを設定
                $action = 'notFound';
            }

            // 認証＆認可・承認
            // 認証
            /*
                authentication メソッドは基本的には無い
                コントローラーに定義されていれば、認証処理を実行
            */
            if (!$this->authentication($controller)) {
                // NG
                $action = 'unauthenticated';
            } else {
                // 認可・承認
                /*
                    autorization メソッドは基本的には無い
                    コントローラーに定義されていれば、認証以外の表示条件を実行
                */
                if (!$this->authorization($controller)) {
                    // NG
                    $action = 'unauthorized';
                }
            }

            // 最終的なアクションの実行
            Dependency::callMethod($controller, $action);
        } catch (\Throwable $throwable) {
            if (empty($controller)) {
                // コントローラーの生成以前に失敗しているなどで存在しない場合
                // デフォルトコントローラーを利用して実行する
                $controller = new $defaultControllerClassName();
            }

            // 例外用アクションを実行
            $controller->exception($throwable);
        }
    }

    /**
     * コントローラを設定
     *
     * コントローラのクラスが見つからなかった場合は設定しない
     * （初期値のデフォルトコントローラがそのまま使用される）
     */
    private function getControllerClassName(string $defaultControllerClassName): string
    {
        // パスカルケースに変換（パス形式にも対応）
        $controllerPath = ConvertCaseService::snakeToPascal(RequestControllerPath);
        // 先頭のスラッシュを削除
        $tempControllerPath = ltrim($controllerPath, '/');
        // パスを分解
        $explodedControllerPath = explode('/', $tempControllerPath);

        // 分解したパスカルケースのパスから、コントローラーの完全修飾クラス名を作る
        $controllerClassName = sprintf('\\App\\Controller\\%sController', implode('\\', $explodedControllerPath));

        if (!class_exists($controllerClassName)) {
            // Appに無い場合、Coreをチェック
            $controllerClassName = sprintf('\\SmallFramework\\Core\\Controller\\%sController', implode('\\', $explodedControllerPath));
        }

        if (!class_exists($controllerClassName)) {
            // クラスが存在していなければデフォルトコントローラを使用する
            return $defaultControllerClassName;
        }

        return $controllerClassName;
    }

    /**
     * 認証（Authentication）
     *
     * コントローラーに authentication メソッドがある場合、
     * その実行結果を認可・承認結果とする
     * （メソッドがない場合は常にOK）
     */
    private function authentication(DefaultController $controller): bool
    {
        // メソッドが無ければ常にtrue、ある場合はその実行結果を認可結果とする
        return !\is_callable([$controller, 'authentication']) ? true : Dependency::callMethod($controller, 'authentication');
    }

    /**
     * 認可・承認（Authorization）
     *
     * コントローラーに authorization メソッドがある場合、
     * その実行結果を認可・承認結果とする
     * （メソッドがない場合は常にOK）
     */
    private function authorization(DefaultController $controller): bool
    {
        // メソッドが無ければ常にtrue、ある場合はその実行結果を認可結果とする
        return !\is_callable([$controller, 'authorization']) ? true : Dependency::callMethod($controller, 'authorization');
    }
}
