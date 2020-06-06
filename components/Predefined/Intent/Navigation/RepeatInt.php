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
use Commune\Ghost\Context\AEventContext;
use Commune\Protocals\HostMsg\IntentMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * 
 * @title 重复对话
 * @desc 重复上一轮的对话
 *
 * @spell #repeat
 * 
 * @example 再说一遍
 * @example 刚才说什么
 * @example 现在说的是啥
 * @example 你问什么
 * @example 刚才说啥
 * 
 */
class RepeatInt extends AEventContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_REPEAT;
    }

    public function action(Dialog $dialog): Operator
    {
        return $dialog->rewind();
    }
}