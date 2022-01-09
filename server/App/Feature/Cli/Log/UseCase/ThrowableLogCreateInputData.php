<?php

declare(strict_types=1);

namespace App\Feature\Cli\Log\UseCase;

use SmallFramework\Core\Entity\Model\ThrowableLogModel;
use SmallFramework\Core\Entity\Service\CommandExecutionService;

class ThrowableLogCreateInputData
{
    private ThrowableLogModel $decodedData;

    public function __construct(string $data)
    {
        $this->decodedData = CommandExecutionService::decode($data);
    }

    public function getDecodedData(): ThrowableLogModel
    {
        return $this->decodedData;
    }
}
