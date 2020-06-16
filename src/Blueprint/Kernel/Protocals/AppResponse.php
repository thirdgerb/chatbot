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

use Commune\Blueprint\Exceptions\CommuneErrorCode;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppResponse extends AppProtocal, CommuneErrorCode
{

    /**
     * 异常码. @see CommuneErrorCode
     * @return int
     */
    public function getErrcode() : int;

    /**
     * 异常消息.
     * @return string
     */
    public function getErrmsg() : string;


    /**
     * @return bool
     */
    public function isSuccess() : bool;


}