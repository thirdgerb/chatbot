<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocals;

use Commune\Protocals\IntercomMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellOutputRequest extends AppRequest
{

    /**
     * @return ShellOutputResponse|null
     */
    public function validate() : ? ShellOutputResponse;

    /**
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * @return string
     */
    public function getBatchId() : string;

    /**
     * @return IntercomMsg[]
     */
    public function getOutputs() : array;

    /**
     * @param IntercomMsg[] $messages
     */
    public function setOutputs(array $messages) : void;

}