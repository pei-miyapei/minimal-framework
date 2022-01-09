<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Json;

class JsonInputData
{
    /**
     * @param mixed $data
     */
    public function __construct(
        private $data
    ) {
    }

    public function getData()
    {
        return $this->data;
    }
}
