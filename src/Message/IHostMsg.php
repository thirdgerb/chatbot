<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message;

use Commune\Protocals\HostMsg;
use Commune\Support\Message\AbsMessage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHostMsg extends AbsMessage implements HostMsg
{
    public static function stub(): array
    {
        return [
            'level' => HostMsg::INFO,
        ];
    }

    public static function validate(array $data): ? string
    {
        return in_array($data['level'] ?? null, HostMsg::LEVELS) ? null : 'invalid level';
    }

    public static function relations(): array
    {
        return [];
    }
}