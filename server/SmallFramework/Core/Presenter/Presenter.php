<?php

declare(strict_types=1);

namespace SmallFramework\Core\Presenter;

use SmallFramework\Core\View\View;

abstract class Presenter
{
    public function __construct(
        protected View $view
    ) {
    }
}
