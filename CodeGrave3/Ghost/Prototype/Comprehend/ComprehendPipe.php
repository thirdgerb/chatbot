<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Comprehend;

use Commune\Ghost\Blueprint\Convo\Conversation;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ComprehendPipe
{
    const HANDLER = 'handler';

    abstract public function handle(
        Conversation $conversation,
        callable $next
    ) : Conversation;
}