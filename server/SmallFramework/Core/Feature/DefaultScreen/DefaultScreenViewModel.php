<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\DefaultScreen;

class DefaultScreenViewModel
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
