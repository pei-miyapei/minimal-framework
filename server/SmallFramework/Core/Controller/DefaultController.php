<?php

declare(strict_types=1);

namespace SmallFramework\Core\Controller;

use Dependency\Dependency;
use SmallFramework\Core\Entity\Collection\MessageCollection;
use SmallFramework\Core\Feature\DefaultScreen\DefaultScreenInputData;
use SmallFramework\Core\Feature\DefaultScreen\DefaultScreenInteractor;

class DefaultController
{
    /** コントローラー内例外の例外処理 */
    public function exception(\Throwable $throwable): void
    {
        // エラー用アクションを実行
        $errorCollection = new MessageCollection();
        $errorCollection->push('エラーが発生しました。');
        $errorCollection->push($throwable->getMessage());
        $this->error($errorCollection);

        // コントローラ外例外の例外処理へ
        throw $throwable;
    }

    /** エラー画面 */
    public function error(?MessageCollection $errorCollection = null): void
    {
        $inputData = new DefaultScreenInputData('/error.php', compact('errorCollection'));
        $this->defaultScreen($inputData);
    }

    /**
     * 404 Not Found
     * コントローラーや指定されたアクションが見つからなかったときなど
     */
    public function notFound(): void
    {
        $inputData = new DefaultScreenInputData('/404.html');
        $this->defaultScreen($inputData);
    }

    /**
     * 未認証
     * unauthenticated (Authentication Required)
     *
     * 401 Unauthorized
     * HTTP 標準では "unauthorized" (不許可) と定義されていますが、
     * 意味的にはこのレスポンスは "unauthenticated" (未認証) です。
     * つまり、クライアントはリクエストされたレスポンスを得るためには認証を受けなければなりません。
     */
    public function unauthenticated(): void
    {
        $inputData = new DefaultScreenInputData('/unauthenticated.html');
        $this->defaultScreen($inputData);
    }

    /**
     * 不許可
     * unauthorized (Authorization Required)
     */
    public function unauthorized(): void
    {
        $inputData = new DefaultScreenInputData('/unauthorized.html');
        $this->defaultScreen($inputData);
    }

    /**
     * ほとんどビジネスロジックの必要ない
     * エラー時(404等)や単純なページを表示する場合用の
     * DefaultScreenInteractor をコールする
     * （App側で置き換えることも可能）
     */
    protected function defaultScreen(DefaultScreenInputData $inputData): void
    {
        $interactor = Dependency::call(DefaultScreenInteractor::class);
        Dependency::callMethod($interactor, 'handle', [$inputData]);
    }
}
