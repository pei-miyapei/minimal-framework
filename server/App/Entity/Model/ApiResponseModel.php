<?php

declare(strict_types=1);

namespace App\Entity\Model;

class ApiResponseModel
{
    public ?int $httpCode;

    public array $header = [];

    public array $explodedHeaders = [];

    public string $body = '';

    public object $decodedBody;
}
