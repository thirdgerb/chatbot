<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Predefined\SystemInts;

use Commune\Message\Blueprint\Tag\MsgLevel;
use Commune\Message\Constants\SystemIntents;
use Commune\Message\Predefined\IIntentMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $errcode
 * @property-read string $errmsg
 */
class IntercomFailureInt extends IIntentMsg
{
    public function __construct(
        string $message = '',
        int $code = 0
    )
    {
        parent::__construct(
            SystemIntents::INTERCOM_FAILURE,
            [
                'errcode' => $code,
                'errmsg' => $message
            ],
            MsgLevel::ERROR
        );
    }


}