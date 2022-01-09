<?php

declare(strict_types=1);

namespace SmallFramework\Core\Dependency;

use SmallFramework\Core\Dependency;

final class SingletonContainer
{
    /**
     * 型宣言のクラス名とシングルトン用のインスタンス
     *
     * キー：完全修飾クラス名
     * 値  ：シングルトン用のインスタンス
     */
    private static array $instanceCollectionForSingleton = [];

    /**
     * 型宣言のクラス名とシングルトン用のインスタンスを設定
     *
     * @return object インスタンスを返却
     */
    public static function setSingletonInstance(object $instance): object
    {
        self::$instanceCollectionForSingleton['\\'.\get_class($instance)] = $instance;

        return $instance;
    }

    /** シングルトン用インスタンスが定義されているかどうかを返す */
    public static function hasSingletonInstance(string $className): bool
    {
        return !empty(self::$instanceCollectionForSingleton[Dependency::addLeadingBackslash($className)]);
    }

    /** シングルトン用インスタンスを返す */
    public static function getSingletonInstance(string $className): ?object
    {
        $className = Dependency::addLeadingBackslash($className);

        // シングルトンの定義があれば返して終了
        if (self::hasSingletonInstance($className)) {
            return self::$instanceCollectionForSingleton[$className];
        }

        return null;
    }
}
