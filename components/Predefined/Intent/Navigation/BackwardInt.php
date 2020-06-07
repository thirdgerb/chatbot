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
 * @title 返回
 * @desc 返回上一步
 *
 * @example 回到上一步
 * @example 返回上一步
 * @example 回到刚才那个问题
 * @example 返回前面的问题
 * @example 再说一次上一个问题
 */
class BackwardInt extends AIntentContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_BACK;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev->backStep(1);
    }

}