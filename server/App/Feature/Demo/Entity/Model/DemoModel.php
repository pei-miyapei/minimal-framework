<?php

declare(strict_types=1);

namespace App\Feature\Demo\Entity\Model;

/**
 * demo
 * デモ用のテーブルです
 *
 * @note scaffold機能で自動作成したクラス定義を調整したもの
 */
class DemoModel
{
    /**
     * @var int(int)
     */
    public int $id = 0;

    /**
     * @var string(varchar:20)
     */
    public string $title = '';

    /**
     * @var string(datetime)
     */
    public string $created_at = '0000-00-00 00:00:00';

    /**
     * @var string(datetime)
     */
    public string $updated_at = '0000-00-00 00:00:00';

    /** 新規登録時用のカラムと値の定形 */
    public function getCollectionForInsert(): array
    {
        $collection = [];
        $collection['id'] = (int) $this->id;
        $collection['title'] = sprintf("'%s'", addslashes($this->title));
        $collection['created_at'] = 'NOW()';
        $collection['updated_at'] = 'NOW()';

        return $collection;
    }

    /**
     * UPDATE時用のカラムと値の定形
     * SET句用の predicate（"column = value"の文字列）を集めた配列を返す
     */
    public function getCollectionForUpdate(): array
    {
        $collection = [];
        $collection['id'] = (int) $this->id;
        $collection['title'] = sprintf("'%s'", addslashes($this->title));
        $collection['updated_at'] = 'NOW()';

        $predicateCollection = [];

        foreach ($collection as $columnName => $value) {
            $predicateCollection[] = sprintf('%s = %s', $columnName, $value);
        }

        return $predicateCollection;
    }

    /**
     * UPSERT時用のカラムと値の定形
     * ON DUPLICATE KEY UPDATE用の predicate（"column = value"の文字列）を集めた配列を返す
     */
    public function getCollectionForUpsert(): array
    {
        $collection = [];
        $collection['title'] = 'VALUES(title)';
        $collection['updated_at'] = 'VALUES(updated_at)';

        $predicateCollection = [];

        foreach ($collection as $columnName => $value) {
            $predicateCollection[] = sprintf('%s = %s', $columnName, $value);
        }

        return $predicateCollection;
    }
}
