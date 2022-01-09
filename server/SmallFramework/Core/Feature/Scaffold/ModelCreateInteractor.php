<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Scaffold;

use SmallFramework\Core\Entity\Service\ConvertCaseService;
use SmallFramework\Core\Entity\Service\HtmlService;
use SmallFramework\Core\Feature\Mysql\Entity\Collection\Show\FullColumnsCollection;
use SmallFramework\Core\Feature\Mysql\Entity\Model\Show\FullColumnsModel;
use SmallFramework\Core\Feature\Mysql\Gateway\DataAccess\ShowRepository;

final class ModelCreateInteractor
{
    private ShowRepository $repository;

    public function handle(ModelCreateInputData $inputData): void
    {
        // リクエストされたテーブル名
        $tableName = $inputData->getTableName();

        if (empty($tableName)) {
            echo 'テーブル名の指定がありません。';

            return;
        }

        $this->repository = new ShowRepository($inputData->getPdo());

        $showFullColumnsCollection = $this->repository->showFullColumns($tableName);

        // 各プロパティ（=カラム）部分の定義を作成
        $propertyTextCollection = $this->makePropertyTextCollection(
            $showFullColumnsCollection,
            $this->getForeignKeyInfo($tableName)
        );

        $propertyTextCollection[] = $this->makeGetCollectionForRegisterFunctions($showFullColumnsCollection);

        // テーブル情報取得
        $tableInfo = $this->repository->showTableStatus($tableName);

        // クラスのコメント
        $temp = empty($tableInfo->Comment) ? [] : explode("\n", $tableInfo->Comment);
        array_unshift($temp, $tableInfo->Name);
        $comment = implode("\n * ", $temp);

        // 最終的なクラス定義を作成
        $classText = ClassCreateService::make(
            'App\\Entity\\Model',
            $comment,
            ConvertCaseService::snakeToPascal($tableInfo->Name).'Model',
            implode("\n\n", $propertyTextCollection)
        );

        // そのまま画面出力
        // @note ファイル出力してもいいけどどうせそのままは使用しない…
        printf('<pre>%s</pre>', HtmlService::escape($classText));
    }

    public function makeGetCollectionForRegisterFunctions(FullColumnsCollection $showFullColumnsCollection): string
    {
        $fieldNameMaxLength = $showFullColumnsCollection->getFieldNameMaxLength();

        $temp = [];
        $temp2 = [];

        foreach ($showFullColumnsCollection as $fullColumnsModel) {
            if ($fullColumnsModel->Extra === 'auto_increment') {
                continue;
            }

            $whiteSpace = str_repeat(' ', $fieldNameMaxLength - $fullColumnsModel->getFieldNameLength());

            if ($fullColumnsModel->getPhpType() === 'int') {
                // 整数
                $temp[] = sprintf(
                    '        $collection["%s"]%s = %s(int) $this->%s;',
                    $fullColumnsModel->Field,
                    $whiteSpace,
                    !$fullColumnsModel->isNullable() ? '' : sprintf('!isset($this->%s)%s ? "NULL" : ', $fullColumnsModel->Field, $whiteSpace),
                    $fullColumnsModel->Field
                );
            } else {
                // 整数以外は文字列形式のまま渡す
                $temp[] = sprintf(
                    '        $collection["%s"]%s = %ssprintf("\'%%s\'", addslashes($this->%s));',
                    $fullColumnsModel->Field,
                    $whiteSpace,
                    !$fullColumnsModel->isNullable() ? '' : sprintf('!isset($this->%s)%s ? "NULL" : ', $fullColumnsModel->Field, $whiteSpace),
                    $fullColumnsModel->Field
                );
            }

            $temp2[] = sprintf(
                '        $collection["%s"]%s = "VALUES(%s)";',
                $fullColumnsModel->Field,
                $whiteSpace,
                $fullColumnsModel->Field
            );
        }

        $result = array_merge(
            [
                '    /**',
                '     * 新規登録時用のカラムと値の定形',
                '     * ',
                '     * @return array',
                '     */',
                '    public function getCollectionForInsert()',
                '    {',
                '        $collection = [];',
            ],
            $temp,
            [
                '',
                '        return $collection;',
                '    }',
                '',
                '    /**',
                '     * UPDATE時用のカラムと値の定形',
                '     * SET句用の predicate（"column = value"の文字列）を集めた配列を返す',
                '     * ',
                '     * @return array',
                '     */',
                '    public function getCollectionForUpdate()',
                '    {',
                '        $collection = [];',
            ],
            $temp,
            [
                '',
                '        $predicateCollection = [];',
                '',
                '        foreach ($collection as $columnName => $value)',
                '        {',
                '            $predicateCollection[] = sprintf("%s = %s", $columnName, $value);',
                '        }',
                '',
                '        return $predicateCollection;',
                '    }',
                '',
                '    /**',
                '     * UPSERT時用のカラムと値の定形',
                '     * ON DUPLICATE KEY UPDATE用の predicate（"column = value"の文字列）を集めた配列を返す',
                '     * ',
                '     * @return array',
                '     */',
                '    public function getCollectionForUpsert()',
                '    {',
                '        $collection = [];',
            ],
            $temp2,
            [
                '',
                '        $predicateCollection = [];',
                '',
                '        foreach ($collection as $columnName => $value)',
                '        {',
                '            $predicateCollection[] = sprintf("%s = %s", $columnName, $value);',
                '        }',
                '',
                '        return $predicateCollection;',
                '    }',
            ]
        );

        return implode("\n", $result);
    }

    /** 外部キー情報取得（プロパティのコメントに使用） */
    private function getForeignKeyInfo(string $tableName): array
    {
        /**
         * CREATE文から正規表現で外部キー情報を抽出
         *
         * @note PREG_SET_ORDER を指定するとマッチごとの情報をループで取りやすい形の配列になる
         */
        $matches = [];
        preg_match_all(
            '/FOREIGN KEY \(`(.+)`\) REFERENCES `(.+)` \(`(.+)`\)/',
            $this->repository->getCreateStatement($tableName),
            $matches,
            PREG_SET_ORDER
        );

        // マッチした情報を配列にまとめる
        $foreignKeyInfo = [];

        foreach ($matches as $row) {
            // キー：対象のカラム
            // 値：外部キーになっている table.column
            $foreignKeyInfo[$row[1]] = $row[2].'.'.$row[3];
        }

        return $foreignKeyInfo;
    }

    /** 各プロパティ（=カラム）部分の定義を作成 */
    private function makePropertyTextCollection(
        FullColumnsCollection $showFullColumnsCollection,
        array $foreignKeyInfo = []
    ): array {
        $propertyTextCollection = [];

        /** @var FullColumnsModel $fullColumnsModel */
        foreach ($showFullColumnsCollection as $fullColumnsModel) {
            // 定義されている初期値（定義されていない場合null）
            $defaultValue = $fullColumnsModel->Default;

            // MySQLの型で分岐し、パーツを作成する
            switch ($fullColumnsModel->getPhpType()) {
                case 'int':
                    // 整数
                    if (!isset($defaultValue) && !$fullColumnsModel->isNullable()) {
                        // NULL許可ではなく初期値の設定が無い場合、初期値0にする
                        $defaultValue = 0;
                    }

                    break;

                case 'float':
                    // 少数
                    if (!isset($defaultValue) && !$fullColumnsModel->isNullable()) {
                        // NULL許可ではなく初期値の設定が無い場合、初期値0にする
                        $defaultValue = '0.0';
                    }

                    break;

                case 'string':
                    switch ($fullColumnsModel->getType()) {
                        case 'date':
                            // 日付（文字列）
                            if (isset($defaultValue)) {
                                $defaultValue = sprintf('"%s"', addslashes($defaultValue));
                            } elseif (!$fullColumnsModel->isNullable()) {
                                // NULL許可ではなく初期値の設定が無い場合、初期値日付0の文字列にする
                                $defaultValue = '"0000-00-00"';
                            }

                            break;

                        case 'datetime':
                        case 'timestamp':
                            // 日付（文字列）
                            // @note timestamp型も取れる値は日時だった
                            if (isset($defaultValue)) {
                                $defaultValue = sprintf('"%s"', addslashes($defaultValue));
                            } elseif (!$fullColumnsModel->isNullable()) {
                                // NULL許可ではなく初期値の設定が無い場合、初期値日時0の文字列にする
                                $defaultValue = '"0000-00-00 00:00:00"';
                            }

                            break;

                        default:
                            // その他の型はPHPでは文字列として扱う
                            if (isset($defaultValue)) {
                                $defaultValue = sprintf('"%s"', addslashes($defaultValue));
                            } elseif (!$fullColumnsModel->isNullable()) {
                                /**
                                 * NULL許可ではなく初期値の設定が無い場合、初期値空文字にする
                                 *
                                 * @note ちなみにNULL許可でも文字列型の場合、MySQLは初期値空文字になっている様子。
                                 */
                                $defaultValue = '""';
                            }

                            break;
                    }

                    break;
            }

            // コメント部分の文言を集める
            $comment = [];

            // MySQLのカラムコメント
            if ($fullColumnsModel->hasComment()) {
                $comment[] = $fullColumnsModel->Comment;
            }

            // その他属性（auto_incrementなど）
            if ($fullColumnsModel->hasExtra()) {
                $comment[] = $fullColumnsModel->Extra;
            }

            // 外部キー制約のコメント
            // @note カラムのコメントと同じ場合は追記しない
            if (isset($foreignKeyInfo[$fullColumnsModel->Field]) && $foreignKeyInfo[$fullColumnsModel->Field] !== $fullColumnsModel->Comment) {
                $comment[] = sprintf('(%s)', $foreignKeyInfo[$fullColumnsModel->Field]);
            }

            // 空行
            if (!empty($comment)) {
                $comment[] = '';
            }

            // 型コメント
            $comment[] = sprintf(
                '@var %s(%s)%s',
                $fullColumnsModel->getPhpType(),
                $fullColumnsModel->getFullType(),
                !$fullColumnsModel->isNullable() ? '' : '|NULL'
            );

            // コメントとプロパティの定義を作成する
            $temp = [];
            $temp[] = '    /**';

            // コメント各行
            foreach ($comment as $line) {
                $temp[] = sprintf('     * %s', $line);
            }

            $temp[] = '     */';
            // プロパティ
            $temp[] = sprintf(
                '    public $%s%s;',
                $fullColumnsModel->Field,
                \strlen($defaultValue) < 1 ? '' : ' = '.$defaultValue
            );
            $temp = implode("\n", $temp);
            // var_dump($fullColumnsModel);
            // var_dump($temp);
            $propertyTextCollection[] = $temp;
        }

        return $propertyTextCollection;
    }
}
