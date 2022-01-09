<?php

declare(strict_types=1);

namespace SmallFramework\Core;

use Iterator;

class PdoStatement extends \PDOStatement implements \IteratorAggregate
{
    protected function __construct(\PDO $pdo)
    {
        $fetchMode = $pdo->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE);

        if (\in_array($fetchMode, [\PDO::FETCH_CLASS, \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE], true)) {
            // \PDO::FETCH_CLASS の場合
            $this->setFetchMode($fetchMode, '\\stdClass');
        } elseif ($fetchMode === \PDO::FETCH_INTO) {
            // \PDO::FETCH_INTO の場合
            $this->setFetchMode($fetchMode, new \stdClass());
        }
    }

    /**
     * 1件ずつ返すジェネレータ
     *
     * {@inheritDoc}
     *
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator(): Iterator
    {
        while ($row = $this->fetch()) {
            yield $row;
        }
        // return new PdoStatementIterator($this);
    }
}
