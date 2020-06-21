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

use Commune\Protocals\HostMsg\Convo\EventMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read array $payload
 */
class IEventMsg extends AbsMessage implements EventMsg
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'payload' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocalId(): string
    {
        return $this->name;
    }


    public function getEventName(): string
    {
        return $this->name;
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
        return StringUtils::normalizeString($this->name);
    }

    public function isEmpty(): bool
    {
        return false;
    }


}