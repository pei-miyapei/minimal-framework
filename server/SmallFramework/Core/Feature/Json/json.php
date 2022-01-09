<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Json;

header('Content-Type: application/json');

/**
 * @var JsonViewModel $viewModel
 */
echo $viewModel->getDataInJson();
