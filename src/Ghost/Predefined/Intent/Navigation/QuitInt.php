<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Predefined\Intent\Navigation;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\AIntentContext;
use Commune\Protocols\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @title 退出多轮对话
 * @desc 退出当前多轮对话
 *
 * @example 再见
 * @example 我走了
 * @example 拜拜
 * @example 我想退出
 */
class QuitInt extends AIntentContext
{

    public static function __name(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_QUIT;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev->quit();
    }

}