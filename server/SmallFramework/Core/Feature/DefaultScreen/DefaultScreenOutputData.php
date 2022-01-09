<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\DefaultScreen;

class DefaultScreenOutputData
{
    public function __construct(private string $templatePath, private array $data = [])
    {
        $this->templatePath = $templatePath;
        $this->data = $data;
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
