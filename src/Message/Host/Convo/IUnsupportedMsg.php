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
use Commune\Support\Struct\Struct;
use Commune\Support\Message\AbsMessage;
use Commune\Protocals\HostMsg\Convo\UnsupportedMsg;

/**
 * 系统不支持的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $type
 */
class IUnsupportedMsg extends AbsMessage implements UnsupportedMsg
{

    public function __construct(string $type = '')
    {
        parent::__construct(['type' => $type]);
    }

    public static function stub(): array
    {
        return [
            'type' => '',
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static($data['type'] ?? '');
    }

    public static function relations(): array
    {
        return [];
    }

    // 不支持的消息不需要广播.
    public function isBroadcasting(): bool
    {
        return false;
    }


    public function getMsgType(): string
    {
        return $this->type;
    }


    public function getNormalizedText(): string
    {
        return '';
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