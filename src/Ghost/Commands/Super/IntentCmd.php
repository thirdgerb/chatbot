<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Commands\Super;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Ghost\Cmd\AGhostCmd;
use Commune\Ghost\Support\ContextUtils;


/**
 * 指定匹配意图的名称.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IntentCmd extends AGhostCmd
{
    const SIGNATURE = 'intent 
       {name : 意图的名称}
    ';

    const DESCRIPTION = '指定命中一个意图的名称.';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $intentName = $message['name'] ?? '';
        $intentName = trim($intentName);
        if (ContextUtils::isValidIntentName($intentName)) {
            $this->cloner->input->comprehension->intention->setMatchedIntent($intentName);

            $this->notice("设置命中意图 $intentName");
            $this->goNext();
            return;
        }

        $this->error("意图名不合法! 输入意图名为: $intentName ");
    }


}