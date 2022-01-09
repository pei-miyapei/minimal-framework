<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Characteristic;

trait BulkUpsertCollectionTrait
{
    /**
     * 一括UPSERT用に、1件のモデルから
     * UPSERT時用のカラムと値の定形を取得
     * ON DUPLICATE KEY UPDATE用の predicate（"column = value"の文字列）を集めた配列を返す
     */
    public function getCollectionForBulkUpsert(): string
    {
        $model = reset($this->collection);

        $collectionForUpsert = $model->getCollectionForUpsert();

        return implode(",\n", $collectionForUpsert);
    }
}
