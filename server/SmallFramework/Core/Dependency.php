<?php

declare(strict_types=1);

namespace SmallFramework\Core;

use SmallFramework\Core\Dependency\InterfaceToClassBindContainer;
use SmallFramework\Core\Dependency\SingletonContainer;

final class Dependency
{
    /** 先頭にバックスラッシュが無ければ付与して返す */
    public static function addLeadingBackslash(string $name): string
    {
        return str_starts_with($name, '\\') ? $name : '\\'.$name;
    }

    /**
     * 型宣言のインターフェース名をクラス名に読み替えるためのマップを設定
     *
     * @param string $interfaceName 完全修飾インターフェース名
     * @param string $className     完全修飾クラス名
     */
    public static function bind(string $interfaceName, string $className): void
    {
        InterfaceToClassBindContainer::setInterfaceToClass($interfaceName, $className);
    }

    /**
     * 型宣言のクラス名とシングルトン用のインスタンスを設定
     *
     * @param object|string $class              インスタンスまたは完全修飾インターフェース/クラス名
     *                                          名前で指定した場合は self::call でインスタンス化してから保持する
     *                                          （インターフェース名の読み替えも実行される）
     * @param array         $specifiedArguments 数字添字配列で引数を部分的に指定可能（キーは0から順）
     *                                          $classを名前で指定した場合のみ有効
     *
     * @return object インスタンスを返却
     */
    public static function singleton($class, array $specifiedArguments = []): object
    {
        if (\is_string($class)) {
            // 文字列の場合はインスタンス化
            $class = self::call($class, $specifiedArguments);
        }

        return SingletonContainer::setSingletonInstance($class);
    }

    /**
     * 指定されたインターフェースまたはクラスに対応するインスタンスを
     * コンストラクターの依存関係を解決した上で取得
     *
     * @param string $fullName           完全修飾インターフェース/クラス名
     * @param array  $specifiedArguments 数字添字配列で引数を部分的に指定可能（キーは0から順）
     */
    public static function call(string $fullName, array $specifiedArguments = []): object
    {
        // 定義されたインターフェース名の場合はクラス名に置き換え
        $className = InterfaceToClassBindContainer::interfaceToClass($fullName);

        if (SingletonContainer::hasSingletonInstance($className)) {
            // シングルトンの定義があれば返して終了
            return SingletonContainer::getSingletonInstance($className);
        }

        return self::callClass($className, $specifiedArguments);
    }

    /**
     * 指定されたクラスのインスタンスを
     * コンストラクターの依存関係を解決した上で取得
     *
     * @param string $className          完全修飾クラス名
     * @param array  $specifiedArguments 数字添字配列で引数を部分的に指定可能（キーは0から順）
     */
    public static function callClass(string $className, array $specifiedArguments = []): object
    {
        $reflectionClass = new \ReflectionClass($className);

        // コンストラクターの定義を取得
        $reflectionMethod = $reflectionClass->getConstructor();

        if (empty($reflectionMethod)) {
            // コンストラクター定義が無い
            // 解決の必要がないのでそのままインスタンス化して返す
            return $reflectionClass->newInstance();
        }

        // 依存性を解決して引数の要素を得る
        // インスタンス化して返す
        return $reflectionClass->newInstanceArgs(self::resolution($reflectionMethod, $specifiedArguments));
    }

    /**
     * 指定されたクラスメソッドを
     * 引数の依存関係を解決した上で実行し、結果を返す
     * （クラスを名前で指定した場合、インスタンスは取得不可）
     *
     * @param object|string $class              インスタンスまたは完全修飾クラス名
     * @param string        $methodName         メソッド名
     * @param array         $specifiedArguments 数字添字配列で引数を部分的に指定可能（キーは0から順）
     */
    public static function callMethod(object | string $class, string $methodName, array $specifiedArguments = []): mixed
    {
        if (\is_string($class)) {
            // 文字列の場合はインスタンス化
            $class = self::call($class);
        }

        // クラスメソッドの定義を取得
        $reflectionMethod = new \ReflectionMethod($class, $methodName);

        // 依存性を解決して引数の要素を得る
        // メソッドを実行し、結果を返す
        return $reflectionMethod->invokeArgs($class, self::resolution($reflectionMethod, $specifiedArguments));
    }

    /**
     * 指定された関数またはクロージャを
     * 引数の依存関係を解決した上で実行し、結果を返す
     *
     * @param \Closure|string $function           関数名またはクロージャ
     * @param array           $specifiedArguments 数字添字配列で引数を部分的に指定可能（キーは0から順）
     */
    public static function callFunction(\Closure | string $function, array $specifiedArguments = []): mixed
    {
        // 関数の定義を取得
        $reflectionFunction = new \ReflectionFunction($function);

        // 依存性を解決して引数の要素を得る
        // 関数を実行し、結果を返す
        return $reflectionFunction->invokeArgs(self::resolution($reflectionFunction, $specifiedArguments));
    }

    /**
     * メソッドまたは関数の引数の要素を配列にまとめて返す
     *
     * 引数に指定があればその要素を使用し、
     * なければ型宣言をもとにインスタンスを作成して使用する
     * （各クラスに必要な引数も再帰的に解決を試みる）
     *
     * @param \ReflectionFunctionAbstract $reflectionMethod   ReflectionMethod または ReflectionFunction
     * @param array                       $specifiedArguments 数字添字配列で引数を部分的に指定可能（キーは0から順）
     */
    private static function resolution(\ReflectionFunctionAbstract $reflectionMethod, array $specifiedArguments = []): array
    {
        $argumentCollection = [];

        // メソッド/関数の引数定義を取得
        // 引数定義ループ
        foreach ($reflectionMethod->getParameters() as $key => $reflectionParameter) {
            if (isset($specifiedArguments[$key])) {
                // あらかじめ引数の指定があればそちらを優先的に使用
                $argumentCollection[] = $specifiedArguments[$key];
            } elseif (isset($specifiedArguments[$reflectionParameter->name])) {
                // 名前付き引数の指定があれば使用
                $argumentCollection[] = $specifiedArguments[$reflectionParameter->name];
            } else {
                // 引数の定義から引数の要素を取得
                // デフォルト値、型宣言のクラス、null の順で該当するものを返す
                $argumentCollection[] = self::getArgument($reflectionParameter);
            }
        }

        return $argumentCollection;
    }

    /**
     * 引数の定義から引数の要素を取得
     * デフォルト値、型宣言のクラス、null の順で該当するものを返す
     */
    private static function getArgument(\ReflectionParameter $reflectionParameter): mixed
    {
        $argument = null;

        if ($reflectionParameter->isDefaultValueAvailable()) {
            // デフォルト値の定義がある場合はデフォルト値を使用
            $argument = $reflectionParameter->getDefaultValue();
        } else {
            // 引数の指定もデフォルト値も無い場合、型宣言からの生成を試みる
            /** @var null|\ReflectionNamedType|\ReflectionUnionType */
            $type = $reflectionParameter->getType();
            $tempReflectionClass = $type !== null && !$type->isBuiltin()
                ? new \ReflectionClass($type->getName())
                : null
            ;

            if (null !== $tempReflectionClass) {
                // デフォルト値が型宣言をもとにインスタンスを生成して使用（再帰）
                $argument = self::call($tempReflectionClass->getName());
            }
        }

        return $argument;
    }
}
