<?php

declare(strict_types=1);

namespace App\Controller\Cli;

use App\Config\Environment\PdoConnection1Interface;
use App\Config\Exception\NotNotifiedException;
use App\Controller\ControllerBase;
use App\Gateway\DataAccess\DualRepository;
use SmallFramework\Core\Dependency;
use SmallFramework\Core\Entity\Service\Request\CommandRequestService;

/**
 * CLI Demo
 */
class CliDemoController extends ControllerBase
{
    public function __construct()
    {
        parent::__construct();

        // PDOコネクション1取得
        $pdo = Dependency::call(PdoConnection1Interface::class);

        // 複数起動チェック
        Dependency::callMethod($this, 'checkDuplicateProcess', [$pdo->getDatabase()]);
    }

    /**
     * php /var/www/server/public/index.php /cli/cli_demo 'aaa'
     */
    public function index(CommandRequestService $request): void
    {
        echo "cli doit!\n";
        echo 'arg: '.var_export($request->getArgv(2), true)."\n";
    }

    /**
     * php /var/www/server/public/index.php /cli/cli_demo?a=otherAction
     */
    public function otherAction(): void
    {
        echo "Cli Other Action\n";
    }

    /**
     * CLIなどで重複起動を回避する例
     *
     * @param mixed $databaseName
     */
    protected function checkDuplicateProcess($databaseName, DualRepository $dualRepository): void
    {
        if (!$dualRepository->getLock($databaseName.':'.RequestControllerPath)) {
            throw new NotNotifiedException('別プロセス起動中のため終了');
        }
    }
}
