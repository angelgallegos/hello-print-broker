<?php

namespace Framework\ORM\Model;

use JsonSerializable;

interface ModelInterface extends JsonSerializable
{
    /**
     * @return array
     */
    public function toResponse(): array;
}