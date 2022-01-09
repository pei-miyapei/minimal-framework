<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Collection;

use Traversable;

/**
 * ファーストクラスコレクションのテンプレートクラス
 */
abstract class FirstClassCollection implements \IteratorAggregate
{
    /** コレクション */
    protected array $collection = [];

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

    public function isEmpty(): bool
    {
        return empty($this->collection);
    }

    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * キーを全て取得
     */
    public function getKeys(): array
    {
        return array_keys($this->collection);
    }

    /**
     * 要素数を返す
     */
    public function length(): int
    {
        return \count($this->collection);
    }

    public function implode(string $delimiter): string
    {
        return implode($delimiter, $this->collection);
    }

    public function slice(int $offset, ?int $length = null): array
    {
        return \array_slice($this->collection, $offset, $length);
    }

    /** array_uniqueによって、コレクションを一意に変換 */
    public function toUnique(): void
    {
        $this->collection = array_unique($this->collection, SORT_REGULAR);
    }

    /** empty()判定によって、空要素を削除 */
    public function removeEmpty(): void
    {
        foreach ($this->collection as $key => $value) {
            if (empty($value)) {
                unset($this->collection[$key]);
            }
        }
    }

    /**
     * @param mixed $key
     */
    public function containsKey($key): bool
    {
        return isset($this->collection[$key]);
    }

    /**
     * @param mixed $value
     */
    public function containsValue($value): bool
    {
        return \in_array($value, $this->collection, true);
    }
}
