<?php

declare(strict_types=1);

namespace App\Controller;

use App\Feature\Demo\UseCase\DemoPageInteractor;
use SmallFramework\Core\Dependency;

/**
 * Demo
 */
class IndexController extends ControllerBase
{
    public function index(DemoPageInteractor $interactor): void
    {
        Dependency::callMethod($interactor, 'handle');
    }

    public function otherAction(): void
    {
        echo 'Other Action Page';
    }
}
