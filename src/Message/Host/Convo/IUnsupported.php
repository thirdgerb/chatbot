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

use Commune\Protocals\Host\Convo\UnsupportedMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Struct\Struct;

/**
 * 系统不支持的消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IUnsupported extends AbsMessage implements UnsupportedMsg
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

    public function getTrimmedText(): string
    {
        return '';
    }

    public function isEmpty(): bool
    {
        return false;
    }


}