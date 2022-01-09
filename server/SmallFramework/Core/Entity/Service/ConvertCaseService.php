<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

final class ConvertCaseService
{
    /**
     * io_channel          → IoChannel
     * shop_db             → ShopDb
     * /path/to/snake_case → /Path/To/SnakeCase
     */
    public static function snakeToPascal(string $text): string
    {
        $text = strtolower($text);
        // パス形式の先頭も置換させる
        $text = str_replace('/', '/_', $text);

        $temp = '';

        foreach (explode('_', $text) as $word) {
            $temp .= ucfirst($word);
        }

        return $temp;
    }

    /**
     * io_channel → ioChannel
     * shop_db    → shopDb
     */
    public static function snakeToCamel(string $text): string
    {
        return lcfirst(self::snakeToPascal($text));
    }

    /**
     * IOChannel → io_channel
     * IoChannel → io_channel
     * ShopDB    → shop_db
     * ShopDb    → shop_db
     * ioChannel → io_channel
     * shopDB    → shop_db
     * shopDb    → shop_db
     */
    public static function toSnake(string $text): string
    {
        /*
            次に[A-Z]が来るまでの[a-z]+（?=の中身は含まれず、再利用されない）
                abcdA → abcd_A
            次に[A-Z][a-z]が来るまでの[A-Z]+
                ABCDAb → ABCD_Ab
            shopDBCollection → shop_db_collection
        */
        $text = preg_replace('/[a-z]+(?=[A-Z])|[A-Z]+(?=[A-Z][a-z])/', '$0_', $text);

        return strtolower($text);
    }
}
