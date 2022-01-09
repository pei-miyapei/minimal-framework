<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Redirection;

class RedirectionOutputData
{
    public function __construct(
        private string $redirectUri
    ) {
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }
}
