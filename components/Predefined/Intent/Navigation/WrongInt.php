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
use Commune\Protocals\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 答非所问
 * @desc 机器人答非所问
 *
 * @example 答非所问
 * @example 胡说八道
 * @example 你胡扯些什么
 * @example 前言不搭后语
 * @example 完全理解错了
 * @example 你没有理解我的意思
 * @example 你搞错了
 */
class WrongInt extends AIntentContext
{

    public static function __name(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_BOT_WRONG;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev
            ->send()
            ->info(DefaultIntents::GUEST_NAVIGATE_BOT_WRONG)
            ->over()
            ->backStep(1);
    }

}
