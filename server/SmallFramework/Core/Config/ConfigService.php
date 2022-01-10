<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config;

use Dependency\Dependency;
use SmallFramework\Core\Entity\Service\ConvertCaseService;

final class ConfigService
{
    /**
     * 実行環境別のConfigファイルに定義した定数を取得、
     * なければ共通のConfigファイルを参照し、
     * いずれもなければ null を返却する
     *
     * ドット記法で 〇〇.定数 を指定した場合、〇〇Configクラスを参照する
     *
     * @return mixed
     */
    public static function getConstant(string $targetName)
    {
        [$configName, $targetName] = self::getNames($targetName);

        // 環境別設定を参照
        $classResourceName = self::getClassResourcesName($configName, $targetName);

        if (\defined($classResourceName)) {
            return \constant($classResourceName);
        }

        // 共通設定を参照
        $classResourceName = self::getClassResourcesName($configName, $targetName, 'Common');

        if (\defined($classResourceName)) {
            return \constant($classResourceName);
        }

        return null;
    }

    /**
     * 実行環境別または共通のConfigファイルの
     * いずれかでcallableかどうか
     */
    public static function isCallable(string $targetName): bool
    {
        [$configName, $targetName] = self::getNames($targetName);

        // 環境別設定を参照
        $classResourceName = self::getClassResourcesName($configName, $targetName);

        if (\is_callable($classResourceName)) {
            return true;
        }

        // 共通設定を参照
        $classResourceName = self::getClassResourcesName($configName, $targetName, 'Common');

        return \is_callable($classResourceName);
    }

    /**
     * 実行環境別のConfigファイルに定義したメソッドがcallableな場合その結果、
     * なければ共通のConfigファイルの同メソッドを実行する
     * （いずれも未定義の場合エラー）
     *
     * @return mixed
     */
    public static function callMethod(string $targetName)
    {
        [$configName, $targetName] = self::getNames($targetName);

        // 環境別設定を参照
        $classResourceName = self::getClassResourcesName($configName, $targetName);

        if (\is_callable($classResourceName)) {
            return \call_user_func($classResourceName);
        }

        // 共通設定を参照
        $classResourceName = self::getClassResourcesName($configName, $targetName, 'Common');

        return \call_user_func($classResourceName);
    }

    private static function getNames(string $targetName): array
    {
        return str_contains($targetName, '.')
            ? explode('.', $targetName, 2)
            : ['', $targetName]
        ;
    }

    private static function getClassResourcesName(string $configName, string $resourceName, string $environmentName = ''): string
    {
        if (empty($environmentName)) {
            $environmentService = Dependency::call('\\SmallFramework\\Core\\Config\\EnvironmentServiceInterface');
            $environmentName = ConvertCaseService::snakeToPascal((string) $environmentService->getEnvironment());
        }

        return sprintf(
            '%s\\Environment\\%s\\%sConfig::%s',
            NamespaceToConfigDirectory,
            $environmentName,
            $configName,
            $resourceName
        );
    }
}
