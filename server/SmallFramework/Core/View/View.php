<?php

declare(strict_types=1);

namespace SmallFramework\Core\View;

use SmallFramework\Core\Dependency;
use SmallFramework\Core\Entity\Service\Request\RequestServiceInterface;

final class View
{
    /** レイアウトビュークラスのインスタンス */
    private LayoutView $layoutView;

    /**
     * 部分ビュー用の任意の名前をキーとした、
     * 部分ビュークラスのインスタンスを保持するコレクションクラス
     */
    private PartialViewCollection $partialViewCollection;

    /** リクエストモデル */
    private RequestServiceInterface $request;

    /** HTMLヘッダーに追加するタグを保持する */
    private array $tagsForAddToHtmlHeader = [];

    public function __construct(RequestServiceInterface $request)
    {
        Dependency::singleton($this);

        $this->request = $request;
        $this->layoutView = new LayoutView();
        $this->partialViewCollection = new PartialViewCollection();
    }

    public function layout(): LayoutView
    {
        return $this->layoutView;
    }

    /**
     * @param string $partialViewName 部分ビュー用の任意の名前（キー）を指定する
     *                                （省略時 "main" ）
     */
    public function partial(string $partialViewName = 'main'): PartialView
    {
        return $this->partialViewCollection->get($partialViewName);
    }

    /** HTMLを生成して返す */
    public function render(): string
    {
        // 各部分ビューのHTMLを生成
        /*
            @note
            部分ビュー内の処理を全て完了してからレイアウトの処理を行うために、
            （ex: 部分ビュー内でレイアウトのタイトルを操作する場合など）

            最終的なHTMLの生成（=レイアウト用のテンプレートの処理）前に
            各部分HTMLの生成（=各部分ビュー用のテンプレートの処理）を完了させておく
            （=部分ビューのHTMLを保持させておく）
        */
        foreach ($this->partialViewCollection->getKeys() as $partialViewName) {
            $this->generatePartialHtml($partialViewName);
        }

        // 最終的なHTMLを生成
        if ($this->layoutView->isTemplateValid()) {
            // レイアウトテンプレートに指定がある場合

            // 変数展開
            if (!$this->layoutView->isDataEmpty()) {
                extract($this->layoutView->getData());
            }

            // 生成
            ob_start();

            require $this->layoutView->getServerPathToTemplate();
            $html = ob_get_clean();

            // 生成したHTMLを返却
            return $html;
        }

        // レイアウトテンプレートに指定がない場合
        // mainコンテンツの内容を返す
        return $this->partial()->getHtml();
    }

    /**
     * 部分ビューのHTMLを生成・取得
     * （レイアウト用テンプレート等の他のテンプレート内からも使用可能）
     *
     * @note
     * ・テンプレート内からViewクラスの要素を操作するため
     * 　この処理（ob_get_clean）はViewクラスでしか行えない。
     *
     * ・部分ビュー内から部分ビューを召喚（generatePartialHtml）する場合もある
     */
    private function generatePartialHtml(string $partialViewName = 'main'): string
    {
        $partialView = $this->partial($partialViewName);

        if (!$partialView->isHtmlEmpty()) {
            // HTML生成済みの場合はそれを返却して終了
            return $partialView->getHtml();
        }

        if (!$partialView->isTemplateValid()) {
            // テンプレートファイルが無効（見つからないか未設定）
            return '';
        }

        // 変数展開
        if (!$partialView->isDataEmpty()) {
            extract($partialView->getData());
        }

        // 生成
        ob_start();

        require $partialView->getServerPathToTemplate();
        $partialHtml = ob_get_clean();

        // 生成したHTMLは保持しておく
        $partialView->setHtml($partialHtml);

        // 生成したHTMLを返却
        return $partialHtml;
    }
}
