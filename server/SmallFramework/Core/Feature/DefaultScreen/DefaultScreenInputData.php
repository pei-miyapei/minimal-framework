<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\DefaultScreen;

class DefaultScreenInputData
{
    public function __construct(
        private string $templatePath,
        private array $data = []
    ) {
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
