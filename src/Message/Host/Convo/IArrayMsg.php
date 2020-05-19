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

use Commune\Protocals\HostMsg;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\Convo\ArrayMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property array $array
 */
class IArrayMsg extends AbsMessage implements ArrayMsg
{
    public static function stub(): array
    {
        return [
            'array' => []
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getArray(): array
    {
        return $this->array;
    }

    public function isBroadcasting(): bool
    {
        return false;
    }

    public function getLevel(): string
    {
        return HostMsg::DEBUG;
    }

    public function getText(): string
    {
        return json_encode($this->array, ArrayAndJsonAble::PRETTY_JSON);
    }

    public function isEmpty(): bool
    {
        return false;
    }


}