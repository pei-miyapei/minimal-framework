<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Command;

class CommandInputData
{
    public function __construct(private ?string $data = null)
    {
        $this->data = $data;
    }

    public function getData(): ?string
    {
        return $this->data;
    }
}
