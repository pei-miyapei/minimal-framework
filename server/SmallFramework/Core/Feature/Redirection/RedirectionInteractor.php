<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Redirection;

class RedirectionInteractor
{
    public function __construct(
        private RedirectionPresenter $presenter
    ) {
    }

    public function handle(RedirectionInputData $inputData): void
    {
        $outputData = new RedirectionOutputData($inputData->getRedirectUri());
        $this->presenter->output($outputData);
    }
}
