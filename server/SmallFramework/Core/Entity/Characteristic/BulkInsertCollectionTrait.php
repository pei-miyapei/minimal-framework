<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Characteristic;

trait BulkInsertCollectionTrait
{
    /** 一括新規登録時用のカラムの配列を取得 */
    public function getColumnsForBulkInsert(): string
    {
        $model = reset($this->collection);

        // 1件毎のInsertの定形を得る
        $collectionForInsert = $model->getCollectionForInsert();

        return sprintf('(%s)', implode(', ', array_keys($collectionForInsert)));
    }

    /** 一括新規登録時用のレコードごとのvalues句の配列 */
    public function getValuesClauseCollectionForBulkInsert(): array
    {
        $valuesClauseCollection = [];

        foreach ($this as $model) {
            // 1件毎のInsertの定形を得る
            $collectionForInsert = $model->getCollectionForInsert();
            $valuesClauseCollection[] = sprintf('(%s)', implode(', ', $collectionForInsert));
        }

        return $valuesClauseCollection;
    }
}
