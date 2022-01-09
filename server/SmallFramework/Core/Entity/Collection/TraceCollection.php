<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Collection;

use Traversable;

class TraceCollection implements \IteratorAggregate
{
    private array $collection = [];

    /**
     * IteratorAggregate ループ用
     *
     * {@inheritDoc}
     *
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->collection);
    }

    /**
     * トレースを記録
     *
     * 指定されたキー単位に保持するが、
     * キーの指定が空ではない場合、キーなしにも保持する。
     */
    public function add(string $comment, string $traceKey = ''): void
    {
        if (!isset($this->collection[$traceKey])) {
            $this->collection[$traceKey] = [];
        }

        $this->collection[$traceKey][] = $comment;

        if ($traceKey !== '') {
            // キーの指定が空ではない（メインストリームではない）場合、
            // メインストリーム（=キーなし）にも記録する。
            $temp = [];
            $temp[] = str_replace("\n", '、', $traceKey).'、TraceNumber：'.(\count($this->collection[$traceKey]) - 1);
            //$temp[] = "";
            //$temp[] = $comment;
            $this->collection[''][] = implode("\n", $temp);
        }
    }

    /**
     * 規定のソート
     *
     * キーの降順に並び替え、
     * キーなしのメインストリームを先頭に移動する
     */
    public function sort(): void
    {
        // キーを参照しやすそうな順番に変えておく
        krsort($this->collection);

        // キーなしのメインストリームは先頭に移動
        $this->moveToBeginning('');
    }

    /** 指定されたキーの要素を先頭に移動 */
    public function moveToBeginning(string $traceKey): void
    {
        if (isset($this->collection[$traceKey])) {
            $this->collection = [$traceKey => $this->collection[$traceKey]] + $this->collection;
        }
    }

    /** 指定されたキーの要素を末尾に移動 */
    public function moveToEnd(string $traceKey): void
    {
        if (isset($this->collection[$traceKey])) {
            // 控えておいて
            $temp = $this->collection[$traceKey];
            // 一旦消し
            unset($this->collection[$traceKey]);
            // 末尾に追加
            $this->collection[$traceKey] = $temp;
        }
    }
}
