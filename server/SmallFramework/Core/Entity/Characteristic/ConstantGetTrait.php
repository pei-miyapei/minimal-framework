<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Characteristic;

trait ConstantGetTrait
{
    /**
     * @return mixed
     */
    public static function getConstant(string $key)
    {
        $key = sprintf('\\%s::%s', __CLASS__, $key);

        return !\defined($key) ? null : \constant($key);
    }
}
