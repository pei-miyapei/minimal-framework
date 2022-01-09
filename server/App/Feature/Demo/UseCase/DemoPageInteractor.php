<?php

declare(strict_types=1);

namespace App\Feature\Demo\UseCase;

use App\Feature\Demo\Gateway\DataAccess\DemosQueryService;
use App\Feature\Demo\Presenter\DemoPagePresenter;

class DemoPageInteractor
{
    public function handle(DemoPagePresenter $presenter, DemosQueryService $queryService): void
    {
        $demos = $queryService->fetchAll();

        $outputData = new DemoPageOutputData(BaseServerPath.'/resources/views/demo/index.php', $demos);
        $presenter->output($outputData);
    }
}
