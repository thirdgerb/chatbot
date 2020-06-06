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
 *
 * @title 退出多轮对话
 * @desc 退出当前多轮对话
 *
 * @spell #quit
 */
class QuitInt extends AEventContext
{

    public static function __name(): string
    {
        return IntentMsg::GUEST_NAVIGATE_QUIT;
    }

    public function action(Dialog $dialog): Operator
    {
        return $dialog->quit();
    }

}