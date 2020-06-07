<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Predefined\Intent\Navigation;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\AIntentContext;
use Commune\Protocals\HostMsg\IntentMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 返回根语境
 * @desc 回到当前对话的根语境
 *
 */
class HomeInt extends AIntentContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_HOME;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev->reset();
    }

}