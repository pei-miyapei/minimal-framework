<?php

declare(strict_types=1);

namespace SmallFramework\Core\View;

final class LayoutView
{
    /**
     * レイアウトビュー用のテンプレートまでのサーバ内部パス
     * （マスターページ）
     */
    private string $serverPathToTemplate = '';

    /** レイアウトビュー用のテンプレート内で使用するデータ */
    private array $data = [];

    /**
     * レイアウトビュー用のテンプレートを設定
     *
     * @param string $serverPathToTemplate テンプレートファイルまでのサーバ内部パス
     */
    public function setTemplate(string $serverPathToTemplate = ''): void
    {
        $this->serverPathToTemplate = $serverPathToTemplate;
    }

    /** レイアウトビュー用のテンプレートまでのサーバ内部パスを取得 */
    public function getServerPathToTemplate(): string
    {
        return $this->serverPathToTemplate;
    }

    /** 部分ビュー用のテンプレートが有効かどうかを返す */
    public function isTemplateValid(): bool
    {
        return is_file($this->serverPathToTemplate);
    }

    /**
     * レイアウトビュー用のテンプレート内で使用するデータを追加
     *
     * @param mixed $value
     */
    public function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /** レイアウトビュー用のテンプレート内で使用するデータを取得 */
    public function getData(): array
    {
        return $this->data;
    }

    /** データが空かどうかを返す */
    public function isDataEmpty(): bool
    {
        return empty($this->data);
    }
}
