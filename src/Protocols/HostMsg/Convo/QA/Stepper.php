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

use Commune\Blueprint\Ghost\Ucl;

/**
 * 询问用户是否要继续.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Stepper extends QuestionMsg
{
    public function getCurrentStep() : int;

    public function getMaxStep() : int;
}