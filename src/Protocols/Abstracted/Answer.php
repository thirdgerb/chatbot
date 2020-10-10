<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\Abstracted;

use Commune\Protocols\HostMsg\Convo\QA\AnswerMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Answer
{

    public function setAnswer(AnswerMsg $answer) : void;

    public function getAnswer() : ? AnswerMsg;

}