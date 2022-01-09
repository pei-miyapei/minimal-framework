<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Collection;

class MessageCollection extends FirstClassCollection
{
    /** コレクションの末尾に追加 */
    public function push(string $message): void
    {
        $this->collection[] = $message;
    }
}
