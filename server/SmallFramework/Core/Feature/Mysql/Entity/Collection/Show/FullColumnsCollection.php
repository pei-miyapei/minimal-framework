<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Mysql\Entity\Collection\Show;

use SmallFramework\Core\Entity\Collection\FirstClassCollection;
use SmallFramework\Core\Feature\Mysql\Entity\Model\Show\FullColumnsModel;

class FullColumnsCollection extends FirstClassCollection
{
    public function __construct(array $collection = [])
    {
        $this->collection = $collection;
    }

    /**
     * フィールド名の最大長を取得
     */
    public function getFieldNameMaxLength(): int
    {
        $fieldNameMaxLength = 0;

        /**
         * @var FullColumnsModel $fullColumnsModel
         */
        foreach ($this->collection as $fullColumnsModel) {
            if ($fieldNameMaxLength < $fullColumnsModel->getFieldNameLength()) {
                $fieldNameMaxLength = $fullColumnsModel->getFieldNameLength();
            }
        }

        return $fieldNameMaxLength;
    }
}
