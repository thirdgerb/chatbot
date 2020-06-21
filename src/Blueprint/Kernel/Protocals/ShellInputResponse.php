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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellInputResponse extends AppResponse, HasInput
{
    /**
     * @return bool
     */
    public function isAsync() : bool;
}