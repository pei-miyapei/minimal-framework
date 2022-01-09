<?php

declare(strict_types=1);

namespace App\Feature\Cli\Log\UseCase;

use App\Config\Environment\PdoConnection1Interface;
use SmallFramework\Core\Config\ConfigService;
use SmallFramework\Core\Gateway\DataAccess\ThrowableLogRepository;

class ThrowableLogCreateInteractor
{
    private ThrowableLogRepository $repository;

    public function __construct(
        PdoConnection1Interface $pdo
    ) {
        $this->repository = new ThrowableLogRepository($pdo);
    }

    public function handle(ThrowableLogCreateInputData $inputData): void
    {
        $throwableLog = $inputData->getDecodedData();
        $this->repository->insert($throwableLog);

        $this->repository->deleteOlderThanDate(
            date('Y-m-d H:i:s', strtotime('-'.ConfigService::getConstant('RetentionDays').' day'))
        );
    }
}
