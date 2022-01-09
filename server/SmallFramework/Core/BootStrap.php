<?php

declare(strict_types=1);

namespace SmallFramework\Core;

// オートローダー
require_once __DIR__.'/Entity/Service/AutoloadService.php';
Entity\Service\AutoloadService::setAutoLoader(\dirname(__DIR__, 2));
