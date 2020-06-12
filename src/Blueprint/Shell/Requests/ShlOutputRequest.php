<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Requests;

use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlOutputRequest extends ShellRequest
{

    public function getInput(): InputMsg;

    /**
     * @return OutputMsg[]
     */
    public function getOutputs() : array;


    /**
     * @param OutputMsg[] $outputs
     */
    public function setOutputs(array $outputs) : void;
}