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

use Commune\Protocals\Intercom\OutputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostResponse extends AppResponse
{

    /**
     * @return string
     */
    public function getBatchId() : string;

    /**
     * @return OutputMsg[]
     */
    public function getOutputs() : array;

}