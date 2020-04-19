<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Predefined\Event;

use Commune\Message\Blueprint\EventMsg;
use Commune\Message\Predefined\AMessage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AEvent extends AMessage implements EventMsg
{
    /**
     * @var array
     */
    protected $payload;

    public function __construct(array $payload = [], float $createdAt = null)
    {
        $this->payload = $payload;
        parent::__construct($createdAt);
    }

    public function getEventName(): string
    {
        return static::getSerializableId();
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function __sleep(): array
    {
        return [
            'payload',
            'createdAt',
        ];
    }


}