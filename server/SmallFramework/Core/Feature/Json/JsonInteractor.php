<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Json;

class JsonInteractor
{
    public function handle(JsonInputData $inputData, JsonPresenter $presenter): void
    {
        $outputData = new JsonOutputData($inputData->getData());
        $presenter->output($outputData);
    }
}
