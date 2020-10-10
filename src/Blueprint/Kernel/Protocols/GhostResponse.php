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

use Commune\Protocols\Intercom\OutputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostResponse extends AppResponse, HasOutputs
{

    /**
     * @return OutputMsg[]
     */
    public function getOutputs() : array;

    /**
     * @param OutputMsg[] $outputs
     */
    public function mergeOutputs(array $outputs) : void;
}