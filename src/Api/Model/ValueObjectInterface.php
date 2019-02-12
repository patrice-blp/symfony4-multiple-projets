<?php

namespace App\Api\Model;

/**
 * Interface ValueObjectInterface
 * @package App\Api\Model
 */
interface ValueObjectInterface
{
    /**
     * @return array
     */
    public function getValues(): array;
}
