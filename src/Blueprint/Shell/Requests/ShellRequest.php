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

use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Shell\Responses\ShellResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellRequest extends AppRequest
{

    /**
     * @param $output
     * @param int $errcode
     * @param string $errmsg
     * @return ShellResponse
     */
    public function success(
        $output,
        int $errcode = AppResponse::SUCCESS,
        string $errmsg = ''
    ) ;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return ShellResponse
     */
    public function response(int $errcode, string $errmsg = '');
}