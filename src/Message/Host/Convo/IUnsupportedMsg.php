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
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\Convo\UnsupportedMsg;
use Commune\Support\Utils\StringUtils;

/**
 * 系统不支持的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $type
 */
class IUnsupportedMsg extends AbsMessage implements UnsupportedMsg
{

    public static function instance(string $type = '')
    {
        return new static(['type' => $type]);
    }

    public static function stub(): array
    {
        return [
            'type' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getMsgType(): string
    {
        return $this->type;
    }

    public function getProtocalId(): string
    {
        return StringUtils::namespaceSlashToDot(UnsupportedMsg::class);
    }


    public function getText(): string
    {
        return $this->getProtocalId();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getLevel(): string
    {
        return HostMsg::NOTICE;
    }


}