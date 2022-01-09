<?php

declare(strict_types=1);

namespace App\Feature\Demo\Presenter;

use SmallFramework\Core\PdoStatement;

class DemoPageViewModel
{
    public function __construct(
        private PdoStatement $demos
    ) {
    }

    public function getDemos(): PdoStatement
    {
        return $this->demos;
    }
}
