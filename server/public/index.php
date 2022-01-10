<?php

declare(strict_types=1);

use App\Config\BasicPreferenceService;
// use SmallFramework\Core\Config\BasicPreferenceService;
use App\Config\EnvironmentService;
// use SmallFramework\Core\Config\EnvironmentService;
use Dependency\Dependency;
use SmallFramework\Core\Config\BasicPreferenceServiceInterface;
use SmallFramework\Core\Config\DatabaseService;
use SmallFramework\Core\Config\DatabaseServiceInterface;
use SmallFramework\Core\Config\EnvironmentServiceInterface;

// 定数：プロジェクトルートまでのサーバ内部パス
define('BaseServerPath', dirname(__DIR__));
// 定数：フロントコントローラー（本ファイル）までのサーバ内部パス
define('ServerPathToFronController', __FILE__);
// 定数：Configディレクトリまでのサーバ内部パス
define('ServerPathToConfigDirectory', BaseServerPath.'/App/Config');

// composer
require_once BaseServerPath.'/vendor/autoload.php';

require_once BaseServerPath.'/SmallFramework/Core/BootStrap.php';

// 各種設定クラスをインターフェースに関連付け
// 基本設定・ハンドラ
Dependency::bind(
    BasicPreferenceServiceInterface::class,
    BasicPreferenceService::class
);

// 環境設定
Dependency::bind(
    EnvironmentServiceInterface::class,
    EnvironmentService::class
);

// DB設定
Dependency::bind(
    DatabaseServiceInterface::class,
    DatabaseService::class
);

$frontController = new \SmallFramework\Core\FrontController();
$frontController->dispatch();
