<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\DefaultScreen;

use SmallFramework\Core\Presenter\Presenter;

class DefaultScreenPresenter extends Presenter
{
    public function output(DefaultScreenOutputData $outputData): void
    {
        $this->view->partial()->setTemplate($outputData->getTemplatePath());
        $viewModel = new DefaultScreenViewModel($outputData->getData());
        $this->view->partial()->setData(compact('viewModel'));
    }
}
