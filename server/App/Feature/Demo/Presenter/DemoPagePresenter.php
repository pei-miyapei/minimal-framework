<?php

declare(strict_types=1);

namespace App\Feature\Demo\Presenter;

use App\Feature\Demo\UseCase\DemoPageOutputData;
use SmallFramework\Core\Presenter\Presenter;

class DemoPagePresenter extends Presenter
{
    public function output(DemoPageOutputData $outputData): void
    {
        $this->view->layout()->setTemplate(BaseServerPath.'/resources/views/demo/layout.php');
        $this->view->partial()->setTemplate($outputData->getTemplatePath());

        $viewModel = new DemoPageViewModel($outputData->getDemos());
        $this->view->partial()->setData(compact('viewModel'));
    }
}
