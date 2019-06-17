<?php

/**
 * Class ListenerConfig
 * @package Commune\Chatbot\Config
 */

namespace Commune\Chatbot\Config\Event;


use Commune\Support\Option;

/**
 * Class ListenerConfig
 * @package Commune\Chatbot\Config
 *
 * @property-read string $event
 * @property-read string[] $listeners
 */
class EventListenerConfig extends Option
{
    const IDENTITY = 'event';

    public static function stub(): array
    {
        return [
            'event' => 'event',
            []
        ];
    }

    public function getId()
    {
        return $this->event;
    }

}