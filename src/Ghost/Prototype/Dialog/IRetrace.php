<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Dialog;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Stage\Retrace;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRetrace extends ADialog implements Retrace
{
    public function __construct(Conversation $conversation, Context $fallback)
    {
        parent::__construct($conversation);
    }

}