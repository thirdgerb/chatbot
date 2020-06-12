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

use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Shell\Responses\ShlInputResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShlInputRequest extends ShellRequest
{
    /**
     * @param $output
     * @param int $errcode
     * @param string $errmsg
     * @return ShlInputResponse
     */
    public function success(
        $output,
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ) : ShlInputResponse;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return ShlInputResponse
     */
    public function response(
        int $errcode,
        string $errmsg = ''
    ) : ShlInputResponse;

}