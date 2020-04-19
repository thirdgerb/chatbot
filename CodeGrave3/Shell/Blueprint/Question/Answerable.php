<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Question;

use Commune\Message\Blueprint\QuestionMsg;
use Commune\Shell\Blueprint\Session\ShellSession;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Answerable extends QuestionMsg
{
    /**
     * 可以自己理解答案的问题消息.
     * 能够主动分析 Session
     *
     * @param ShellSession $session
     * @return ShellSession|null
     */
    public function parse(ShellSession $session) : ShellSession;
}