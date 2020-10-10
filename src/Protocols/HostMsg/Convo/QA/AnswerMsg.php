<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\HostMsg\Convo\QA;

use Commune\Protocols\HostMsg\Convo\VerbalMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AnswerMsg extends VerbalMsg
{
    public function getAnswer() : string;

    /**
     * @return mixed
     */
    public function getChoice();

    public function getRoute() : ? string;
}