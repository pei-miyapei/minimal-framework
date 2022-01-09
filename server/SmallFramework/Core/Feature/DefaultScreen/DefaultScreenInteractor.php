<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\DefaultScreen;

class DefaultScreenInteractor
{
    public function handle(DefaultScreenInputData $inputData, DefaultScreenPresenter $presenter): void
    {
        $outputData = new DefaultScreenOutputData($inputData->getTemplatePath(), $inputData->getData());
        $presenter->output($outputData);
    }
}
