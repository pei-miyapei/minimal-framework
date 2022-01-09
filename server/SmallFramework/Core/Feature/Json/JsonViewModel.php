<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Json;

use SmallFramework\Core\Entity\Service\JsonService;

class JsonViewModel
{
    /**
     * @param mixed $data
     */
    public function __construct(
        private $data
    ) {
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getDataInJson()
    {
        return JsonService::encode($this->data);
    }
}
