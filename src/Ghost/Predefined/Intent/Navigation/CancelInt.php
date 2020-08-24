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
use Commune\Protocals\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * 
 * @title 退出
 * @desc 退出语境
 *
 * @example 退出
 * @example 我要退出
 * @example cancel
 * @example 不玩了
 * @example 取消
 * @example 取消任务
 * @example 我要取消
 */
class CancelInt extends AIntentContext
{

    public static function __name(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_CANCEL;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev->cancel();
    }

}