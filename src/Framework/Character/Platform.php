<?php

/**
 * Class Platform
 * @package Commune\Chatbot\Charactor
 */

namespace Commune\Chatbot\Framework\Character;


abstract class Platform
{
    final public function __construct()
    {
    }

    public function getId() : string
    {
        return static::class;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId()
        ];
    }
}