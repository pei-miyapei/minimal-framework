<?php

declare(strict_types=1);

namespace SmallFramework\Core\Controller\Scaffold;

use Dependency\Dependency;
use SmallFramework\Core\Config\DatabaseServiceInterface;
use SmallFramework\Core\Config\EnvironmentServiceInterface;
use SmallFramework\Core\Controller\DefaultController;
use SmallFramework\Core\Entity\Service\Request\RequestServiceInterface;
use SmallFramework\Core\Feature\Scaffold\ModelCreateInputData;
use SmallFramework\Core\Feature\Scaffold\ModelCreateInteractor;

final class CreateModelController extends DefaultController
{
    /**
     * @throws \RuntimeException
     */
    public function __construct(EnvironmentServiceInterface $environmentService)
    {
        // PDOコネクション初期化
        Dependency::call(
            DatabaseServiceInterface::class,
            [$environmentService->getEnvironment()]
        );

        if ($environmentService->isProduction()) {
            throw new \RuntimeException('本番環境で実行することはできません。');
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function index(RequestServiceInterface $request): void
    {
        try {
            $connectionNumber = (int) $request->get('connection');

            $connectionInterfaceName = sprintf(
                '%s\\Environment\\PdoConnection%sInterface',
                NamespaceToConfigDirectory,
                empty($connectionNumber) ? 1 : $connectionNumber
            );

            $pdo = null;

            if (interface_exists($connectionInterfaceName)) {
                $pdo = Dependency::call($connectionInterfaceName);
            }

            if (empty($pdo)) {
                throw new \RuntimeException('コネクションクラスが見つかりませんでした。');
            }

            $inputData = new ModelCreateInputData($pdo, $request->get('table'));
            Dependency::callMethod(ModelCreateInteractor::class, 'handle', [$inputData]);
        } catch (\Throwable $throwable) {
            echo (string) $throwable;
        }
    }
}
