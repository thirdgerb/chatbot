<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Session;

use Commune\Framework\Blueprint\Session\SessionStorage;
use Commune\Message\Blueprint\QuestionMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellStorage extends SessionStorage
{

    public function setQuestion(QuestionMsg $question) : void;

    public function getQuestion() : ? QuestionMsg;

}