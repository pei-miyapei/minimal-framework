<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

class ObjectService
{
    /**
     * オブジェクト（または配列）を全て配列にして返す
     * 指定により空要素を破棄できる
     *
     * @param bool $discardNullValueAndEmptyStringKey null値と空文字のキーを破棄するオプション
     */
    public static function toArray(array | object $argument, bool $discardNullValueAndEmptyStringKey): array
    {
        // 子要素まで全て配列化
        $array = json_decode(json_encode($argument), true);

        if (!$discardNullValueAndEmptyStringKey) {
            return $array;
        }

        $result = [];

        foreach ($array as $key => $value) {
            $result = self::recursiveConvertToArray($result, $key, $value);
        }

        return $result;
    }

    /**
     * 配列（またはオブジェクト）を全てオブジェクトにして返す
     * 指定により空要素を破棄できる
     *
     * @param bool $discardNullValueAndEmptyStringKey null値と空文字のキーを破棄するオプション
     */
    public static function toObject(array | object $argument, bool $discardNullValueAndEmptyStringKey): object
    {
        if ($discardNullValueAndEmptyStringKey) {
            /** @note 空要素の削除にどのみち配列にする必要があるため、全部配列化する処理を流用 */
            $argument = self::toArray($argument, true);
        }

        // 子要素まで全てオブジェクト化
        return json_decode(json_encode($argument));
    }

    /**
     * オブジェクトまたは配列のキーを整えて列挙した文字列にして返す
     *
     * @param mixed $argument
     */
    public static function dump($argument): string
    {
        $result = [];

        if (is_scalar($argument)) {
            return (string) $argument;
        }
        if (!\is_object($argument) && !\is_array($argument)) {
            //return var_export($argument, true);
            ob_start();
            var_dump($argument);

            return (string) ob_get_clean();
        }

        // 空要素を破棄して配列化
        $argument = self::toArray($argument, true);

        // 整えて返す
        foreach ($argument as $key => $value) {
            $result[] = self::recursiveDump($key, $value);
        }

        return implode("\n", $result);
    }

    /**
     * 再帰的に値を検証し、スカラーのみの配列を作成する
     *
     * @param mixed $value
     */
    private function recursiveConvertToArray(array $parent, string $key, $value): array
    {
        switch (true) {
            case !isset($value) || $value === '':
                // 値のないキーを無視
                break;

            case \is_array($value):
                $parent[$key] = [];

                foreach ($value as $childKey => $childValue) {
                    $parent[$key] = self::recursiveConvertToArray($parent[$key], $childKey, $childValue);
                }

                if ($parent[$key] === []) {
                    // 空オブジェクト・空配列を削除
                    unset($parent[$key]);
                }

                break;

            default:
                $parent[$key] = $value;

                break;
        }

        return $parent;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    private function recursiveDump($key, $value, string $prefix = ''): string
    {
        switch (true) {
            case \is_array($value):
                $result = [];

                foreach ($value as $childKey => $childValue) {
                    $result[] = self::recursiveDump($childKey, $childValue, $prefix.'　');
                }

                return sprintf(
                    "%s%s：\n%s",
                    $prefix,
                    $key,
                    implode("\n", $result)
                );

            default:
                return sprintf('%s%s：%s', $prefix, $key, $value);
        }
    }
}
