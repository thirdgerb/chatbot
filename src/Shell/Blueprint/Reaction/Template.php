<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Reaction;

use Commune\Messages\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Template
{

    /**
     * @param Reaction $reaction
     * @return Message[]
     */
    public function render(Reaction $reaction) : array;

}