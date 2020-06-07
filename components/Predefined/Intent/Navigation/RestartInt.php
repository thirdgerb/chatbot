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
 * @title 重启语境
 * @desc 当前语境从头开始对话
 */
class RestartInt extends AIntentContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_RESTART;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev->goStage('');
    }

}