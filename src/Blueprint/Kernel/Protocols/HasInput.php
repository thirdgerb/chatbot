<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocols;

use Commune\Protocols\Comprehension;
use Commune\Protocols\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface HasInput
{

    /**
     * @return InputMsg
     */
    public function getInput() : InputMsg;

    /**
     * 环境变量
     * @return array
     */
    public function getEnv() : array;

    /**
     * @return string
     */
    public function getEntry() : string;

    /**
     * @return Comprehension
     */
    public function getComprehension() : Comprehension;

}