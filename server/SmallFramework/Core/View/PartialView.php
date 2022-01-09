<?php

declare(strict_types=1);

namespace SmallFramework\Core\View;

final class PartialView
{
    /** 部分ビュー用のテンプレートまでのサーバ内部パス */
    private string $serverPathToTemplate = '';

    /** 部分ビュー用のテンプレート内で使用するデータ */
    private array $data = [];

    /** 生成された部分ビューのHTMLを保持 */
    private string $html = '';

    /**
     * 部分ビュー用のテンプレートを設定
     *
     * @param string $serverPathToTemplate テンプレートファイルまでのサーバ内部パス
     */
    public function setTemplate(string $serverPathToTemplate): void
    {
        $this->serverPathToTemplate = $serverPathToTemplate;
    }

    /** 部分ビュー用のテンプレートまでのサーバ内部パスを取得 */
    public function getServerPathToTemplate(): string
    {
        return $this->serverPathToTemplate;
    }

    /** 部分ビュー用のテンプレートが有効かどうかを返す */
    public function isTemplateValid(): bool
    {
        return is_file($this->serverPathToTemplate);
    }

    /** 部分ビュー用のテンプレート内で使用するデータを一括設定 */
    public function setData(array $data = []): void
    {
        $this->data = $data;
    }

    /**
     * 部分ビュー用のテンプレート内で使用するデータを追加
     *
     * @param mixed $value
     */
    public function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /** 部分ビュー用のテンプレート内で使用するデータを取得 */
    public function getData(): array
    {
        return $this->data;
    }

    /** データが空かどうかを返す */
    public function isDataEmpty(): bool
    {
        return empty($this->data);
    }

    /** 生成された部分ビューのHTMLを設定 */
    public function setHtml(string $html): void
    {
        $this->html = $html;
    }

    /** 設定した部分ビューのHTMLを取得 */
    public function getHtml(): string
    {
        return $this->html;
    }

    /** HTMLが未設定かどうかを返す */
    public function isHtmlEmpty(): bool
    {
        return empty($this->html);
    }
}
