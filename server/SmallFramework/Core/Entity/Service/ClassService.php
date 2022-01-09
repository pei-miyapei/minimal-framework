<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

final class ClassService
{
    /** 名前空間を含む完全修飾クラス名を返す */
    public static function getNamespaceAndClassName(object | string $instance): string
    {
        return \get_class($instance);
    }

    /** 名前空間を含まないクラス名のみを返す */
    public static function getClassName(object | string $instance): string
    {
        $temp = explode('\\', \is_string($instance) ? $instance : \get_class($instance));

        return implode('', \array_slice($temp, -1));
    }
}
