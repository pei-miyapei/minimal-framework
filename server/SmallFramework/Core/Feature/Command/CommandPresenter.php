<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Command;

use SmallFramework\Core\Presenter\Presenter;
use SmallFramework\Core\View\View;

class CommandPresenter extends Presenter
{
    public function __construct(View $view)
    {
        // 出力なし
        $view->layout()->setTemplate();
    }
}
