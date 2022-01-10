<?php

declare(strict_types=1);

namespace App\Controller\Cli;

use App\Controller\ControllerBase;
use App\Feature\Cli\Log\UseCase\ThrowableLogCreateInputData;
use App\Feature\Cli\Log\UseCase\ThrowableLogCreateInteractor;
use Dependency\Dependency;
use SmallFramework\Core\Entity\Service\Request\CommandRequestService;

/**
 * 例外ログ記録
 */
class LogController extends ControllerBase
{
    public function throwableLog(CommandRequestService $request, ThrowableLogCreateInteractor $interactor): void
    {
        $inputData = new ThrowableLogCreateInputData($request->getArgv(2));
        Dependency::callMethod($interactor, 'handle', [$inputData]);
    }
}
