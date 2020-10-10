<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo;

use Commune\Protocols\HostMsg\Convo\EventMsg;
use Commune\Protocols\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $eventName
 * @property-read array $payload
 */
class IEventMsg extends AbsMessage implements EventMsg
{
    public static function instance(string $eventName, array $payload = [])
    {
        return new static(['eventName' => $eventName, 'payload' => $payload]);
    }

    public static function stub(): array
    {
        return [
            'eventName' => '',
            'payload' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocolId(): string
    {
        return $this->eventName;
    }


    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function isBroadcasting(): bool
    {
        return false;
    }

    public function getLevel(): string
    {
        return HostMsg::INFO;
    }

    public function getText(): string
    {
        return StringUtils::normalizeString($this->eventName);
    }

    public function isEmpty(): bool
    {
        return empty($this->payload);
    }


}