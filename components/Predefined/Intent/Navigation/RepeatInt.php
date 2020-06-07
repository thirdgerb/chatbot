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
 * @title 重复对话
 * @desc 重复上一轮的对话
 *
 * @example 再说一遍
 * @example 刚才说什么
 * @example 现在说的是啥
 * @example 你问什么
 * @example 刚才说啥
 * 
 */
class RepeatInt extends AIntentContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_REPEAT;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev->reactivate();
    }
}