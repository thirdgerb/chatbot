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
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Message\AbsMessage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property string $api
 * @property array $params
 */
class IApiMsg extends AbsMessage implements ApiMsg
{
    public static function stub(): array
    {
        return [
            'api' => '',
            'params' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getProtocalId(): string
    {
        return $this->getApiName();
    }


    public function getApiName(): string
    {
        return $this->api;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getLevel(): string
    {
        // 不能渲染就不要了.
        return HostMsg::DEBUG;
    }

    public function getText(): string
    {
        return $this->toJson(ArrayAndJsonAble::PRETTY_JSON);
    }

    public function isEmpty(): bool
    {
        return false;
    }


}