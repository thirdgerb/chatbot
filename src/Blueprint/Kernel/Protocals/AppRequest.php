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
interface AppRequest extends AppProtocal
{

    /**
     * 自己校验, 如果出错则返回响应.
     * @return AppResponse|null
     */
    public function validate();

    /**
     * @return bool
     */
    public function isStateless() : bool;

    /**
     * @return string
     */
    public function getSessionId() : string;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return AppResponse
     */
    public function response(int $errcode = AppResponse::SUCCESS, string $errmsg = '');

}