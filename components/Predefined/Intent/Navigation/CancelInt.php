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
 * @title 取消
 * @desc 取消当前语境
 *
 * @spell #cancel
 *
 * @example 退出
 * @example 我要退出
 * @example cancel
 * @example 不玩了
 * @example 取消
 * @example 取消任务
 * @example 我要取消
 */
class CancelInt extends AEventContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_CANCEL;
    }

    public function action(Dialog $dialog): Operator
    {
        return $dialog->cancel();
    }

}