<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg\Convo;

use Commune\Protocals\HostMsg;

/**
 * 异步任务投递的结果. 会触发目标语境的 receive 方法.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface JobMsg extends HostMsg
{
    const FULFILL = 0;
    const CANCELED = 1;

    public function getContextUcl() : string;

    public function getCode() : int;

    public function getMessage() : string;

    public function getData() : array;

}