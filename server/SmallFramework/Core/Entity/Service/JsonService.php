<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

class JsonService
{
    /**
     * json_encodeのラッパー
     *
     * @param mixed $value
     * @param ?int  $options デフォルト JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
     */
    public static function encode($value, ?int $options = null, int $depth = 512): string | false
    {
        if (!isset($options)) {
            $options = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        }

        return json_encode($value, $options);
    }
}
