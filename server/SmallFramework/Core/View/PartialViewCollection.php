<?php

declare(strict_types=1);

namespace SmallFramework\Core\View;

use SmallFramework\Core\Entity\Collection\FirstClassCollection;

class PartialViewCollection extends FirstClassCollection
{
    /**
     * 部分ビュー用の任意の名前で
     * 対応するPartialViewクラスのインスタンスを取得
     *
     * インスタンスが未設定の場合は設定してから取得する
     *
     * @param string $partialViewName 部分ビュー用の任意の名前（キー）を指定する
     *                                （省略時 "main" ）
     */
    public function get(string $partialViewName = 'main'): PartialView
    {
        if (!isset($this->collection[$partialViewName])) {
            $this->collection[$partialViewName] = new PartialView();
        }

        return $this->collection[$partialViewName];
    }
}
