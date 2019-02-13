<?php

/**
 * Class Platform
 * @package Commune\Chatbot\Charactor
 */

namespace Commune\Chatbot\Framework\Character;


abstract class Platform
{
    public function getId() : string
    {
        return static::class;
    }
    
    public function getName() : string
    {
        return static::class;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName()
        ];
    }
}
