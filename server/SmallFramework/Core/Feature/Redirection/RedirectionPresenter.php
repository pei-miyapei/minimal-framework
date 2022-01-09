<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Redirection;

class RedirectionPresenter
{
    public function output(RedirectionOutputData $outputData): void
    {
        $redirectUri = $outputData->getRedirectUri();

        if (!empty($redirectUri)) {
            header('Location: '.$redirectUri);
        }
    }
}
