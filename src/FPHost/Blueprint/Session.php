<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\FPHost\Blueprint;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\FPHost\Blueprint\Session\History;


/**
 * 会话相关的历史管理
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Session
{

    public function conversation() : Conversation;

    public function incomingMessage() : IncomingMessage;

    public function nlu() : NLU;

    public function history() : History;


}