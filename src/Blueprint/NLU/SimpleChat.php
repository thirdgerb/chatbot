<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\NLU;


/**
 * 简单闲聊.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SimpleChat extends NLUService
{

    public function reply(string $query) : string;

    public function learn(string $query, string $reply) : void;

}