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
interface ShellInputRequest extends AppRequest, HasInput
{
    /**
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return ShellInputResponse
     */
    public function response(
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ) : ShellInputResponse;

}