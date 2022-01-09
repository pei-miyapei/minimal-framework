<?php

declare(strict_types=1);

namespace App\Feature\Demo\UseCase;

use SmallFramework\Core\PdoStatement;

class DemoPageOutputData
{
    public function __construct(
        private string $templatePath,
        private PdoStatement $demos
    ) {
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    public function getDemos(): PdoStatement
    {
        return $this->demos;
    }
}
