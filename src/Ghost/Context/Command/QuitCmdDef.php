<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Command;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class QuitCmdDef extends AContextCmdDef
{
    public static function stub(): array
    {
        return [
            'desc' => '退出对话',
            'signature' => 'quit',
        ];
    }

    public function handle(
        Dialog $dialog,
        CommandMsg $message
    ): ? Operator
    {
        return $dialog->quit();
    }

}