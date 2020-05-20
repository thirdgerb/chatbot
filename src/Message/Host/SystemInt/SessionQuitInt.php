<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\SystemInt;

use Commune\Message\Host\IIntentMsg;
use Commune\Protocals\HostMsg;
use Commune\Support\Struct\Struct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SessionQuitInt extends IIntentMsg
{
    public function __construct()
    {
        parent::__construct(
            HostMsg\IntentMsg::SYSTEM_SESSION_QUIT,
            [],
            HostMsg::DEBUG
        );
    }

    public static function create(array $data = []): Struct
    {
        return parent::create();
    }

}