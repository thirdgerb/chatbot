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
use Commune\Ghost\Blueprint\Stage\Intend;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntend extends ADialog implements Intend
{
    public function __construct(Conversation $conversation, Context $intending)
    {
        parent::__construct($conversation);
    }

}