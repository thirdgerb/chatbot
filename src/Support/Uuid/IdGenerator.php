<?php


namespace Commune\Support\Uuid;


/**
 * Interface IdGenerator
 * @package Commune\Support\Uuid
 */
interface IdGenerator
{

    /**
     * @return string
     */
    public function createUuId();

}