<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Mysql\Entity\Model\Show;

class FullColumnsModel
{
    public string $Field = '';

    public string $Type = '';

    public ?string $Collation = '';

    public string $Null = '';

    public string $Key = '';

    public ?string $Default = '';

    public string $Extra = '';

    public string $Privileges = '';

    public string $Comment = '';

    /** MySQLの長さ、unsignedなども含んだ形で型を返す。 */
    public function getFullType(): string
    {
        return str_replace(['(', ')'], [':', ''], $this->Type);
    }

    /** MySQLの型を返す。 */
    public function getType(): string
    {
        // MySQLの型に相当する部分のみを取るための長さ
        $length = strpos($this->Type, '(');

        if (!$length) {
            // text型など長さの定義がない場合、型の文言全体
            $length = \strlen($this->Type);
        }

        // MySQLの型で分岐し、パーツを作成する
        return substr($this->Type, 0, $length);
    }

    /** null許可カラムか */
    public function isNullable(): bool
    {
        return $this->Null === 'YES';
    }

    public function getPhpType(): string
    {
        switch ($this->getType()) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                // 整数
                return 'int';

            case 'float':
            case 'double':
            case 'decimal':
                // 少数
                return 'float';

            case 'date':
            case 'datetime':
            case 'timestamp':
            default:
                // @note timestamp型も取れる値は日時だった
                return 'string';
        }
    }

    public function hasComment(): bool
    {
        return $this->Comment !== '';
    }

    public function hasExtra(): bool
    {
        return $this->Extra !== '';
    }

    public function getFieldNameLength(): int
    {
        return \strlen($this->Field);
    }
}
