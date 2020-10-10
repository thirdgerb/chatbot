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

use Commune\Protocols\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Protocols\HostMsg\Convo\Media\AudioMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $resource
 */
class IAudioMsg extends AbsMessage implements AudioMsg
{
    public static function instance(string $resource)
    {
        return new static(['resource' => $resource]);
    }

    public static function stub(): array
    {
        return [
            'resource' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocolId(): string
    {
        return $this->resource;
    }


    public function getText(): string
    {
        return $this->resource;
    }

    public function isBroadcasting(): bool
    {
        return true;
    }

    public function getResource(): string
    {
        return $this->resource;
    }


    public function isEmpty(): bool
    {
        return empty($this->_data['resource']);
    }

    public function getLevel(): string
    {
        return HostMsg::INFO;
    }
}