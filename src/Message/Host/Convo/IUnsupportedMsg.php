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
use Commune\Protocals\Host\Convo\UnsupportedMsg;

/**
 * 系统不支持的消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $type      消息的类型.
 * @property string $level         消息的级别.
 */
class IUnsupportedMsg extends AbsMessage implements UnsupportedMsg
{

    public function __construct(string $type = '', string $level = HostMsg::NOTICE)
    {
        parent::__construct(['type' => $type, 'level' => $level]);
    }

    public static function stub(): array
    {
        return [
            'type' => '',
            'level' => HostMsg::NOTICE
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static($data['type'] ?? '', $data['level'] ?? HostMsg::NOTICE);
    }


    public static function relations(): array
    {
        return [];
    }

    public function getNormalizedText(): string
    {
        return $this->toJson();
    }

    public function isEmpty(): bool
    {
        return false;
    }


}