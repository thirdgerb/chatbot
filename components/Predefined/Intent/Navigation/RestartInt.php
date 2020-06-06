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
 * @title 重启语境
 * @desc 当前语境从头开始对话
 *
 * @spell #restart
 */
class RestartInt extends AEventContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_RESTART;
    }

    public function action(Dialog $dialog): Operator
    {
        return $dialog->goStage('');
    }

}