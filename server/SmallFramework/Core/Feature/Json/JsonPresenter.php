<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Json;

use SmallFramework\Core\Presenter\Presenter;

class JsonPresenter extends Presenter
{
    public function output(JsonOutputData $outputData): void
    {
        $this->view->layout()->setTemplate();
        $this->view->partial()->setTemplate(__DIR__.'/json.php');
        $viewModel = new JsonViewModel($outputData->getData());
        $this->view->partial()->setData(compact('viewModel'));
    }
}
