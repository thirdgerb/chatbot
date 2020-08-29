<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd\User;
use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Kernel\GhostCmd\AGhostCmd;
use Commune\Protocals\Abstracted\Intention;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsIntentCmd extends AGhostCmd
{
    abstract protected function getIntentName() : string;

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $intentName = $this->getIntentName();
        $comprehension = $this->cloner->comprehension;
        $comprehension->intention->setMatchedIntent($intentName);
        $comprehension->handled(
            Intention::class,
            get_class($pipe),
            true
        );

        $this->goNext();
    }


}