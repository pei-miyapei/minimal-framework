<?php

declare(strict_types=1);

namespace SmallFramework\Core\Dependency;

use SmallFramework\Core\Dependency;

final class InterfaceToClassBindContainer
{
    /**
     * 型宣言のインターフェース名をクラス名に読み替えるためのマップ
     *
     * キー：完全修飾インターフェース名
     * 値  ：完全修飾クラス名
     */
    private static array $interfaceToClass = [];

    /**
     * 型宣言のインターフェース名をクラス名に読み替えるためのマップを設定
     *
     * @param string $interfaceName 完全修飾インターフェース名
     * @param string $className     完全修飾クラス名
     */
    public static function setInterfaceToClass(string $interfaceName, string $className): void
    {
        // 先頭にバックスラッシュを付与
        $interfaceName = Dependency::addLeadingBackslash($interfaceName);
        $className = Dependency::addLeadingBackslash($className);

        // 一応インターフェースとクラスの指定であることが確認できたものだけ
        if (interface_exists($interfaceName) && class_exists($className)) {
            self::$interfaceToClass[$interfaceName] = $className;
        }
    }

    /**
     * 定義されたインターフェース名の場合はクラス名に置き換えて返却する
     * 定義されていない場合は指定された値をそのまま返却する
     *
     * @param string $fullName 完全修飾インターフェース名
     */
    public static function interfaceToClass(string $fullName): string
    {
        $fullName = Dependency::addLeadingBackslash($fullName);

        // 定義されたインターフェース名の場合はクラス名に置き換え
        if (empty(self::$interfaceToClass[$fullName])) {
            return $fullName;
        }

        return self::$interfaceToClass[$fullName];
    }
}
