<?php

declare(strict_types=1);

namespace SmallFramework\Core;

/**
 * yieldが遣えないPHP5.5未満用
 *
 * PDOStatementをforeachする際に、
 * 拡張したPDOStatementクラスのfetchが適用されるようにする
 */
class PdoStatementIterator implements \Iterator
{
    /** 行番号インデックス */
    private int $rowCount = 0;

    public function __construct(
        private \PDOStatement $pdoStatement
    ) {
    }

    /**
     * 現在の要素を返す
     *
     * {@inheritDoc}
     *
     * @see \Iterator::current()
     */
    public function current()
    {
        return $this->pdoStatement->fetch();
    }

    /**
     * 現在の要素のキーを返す
     *
     * {@inheritDoc}
     *
     * @see \Iterator::key()
     */
    public function key()
    {
        return $this->rowCount;
    }

    /**
     * 現在位置を次の要素に移動
     *
     * {@inheritDoc}
     *
     * @see \Iterator::next()
     */
    public function next(): void
    {
        ++$this->rowCount;
    }

    /**
     * イテレータの最初の要素に巻き戻す
     * PDOではカーソルを先頭に戻すことはできないため使用不可
     *
     * {@inheritDoc}
     *
     * @see \Iterator::rewind()
     */
    public function rewind(): void
    {
    }

    /**
     * 現在位置が有効かどうかを返す
     *
     * {@inheritDoc}
     *
     * @see \Iterator::valid()
     */
    public function valid(): bool
    {
        return $this->rowCount < $this->pdoStatement->rowCount();
    }
}
