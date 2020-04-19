<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Convo;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ConvoInstance
{

    public function isInstanced() : bool;

    /**
     * @param Conversation $conversation
     * @return static
     */
    public function toInstance(Conversation $conversation) : ConvoInstance;

}