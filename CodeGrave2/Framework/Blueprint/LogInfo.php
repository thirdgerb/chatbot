<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;


/**
 * 生成各种日志用语. 也是避免写死的不好, 哪里都要改.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface LogInfo
{
    public function serverIsNotDuplex() : string;

    public function serverCanNotSendOffline() : string;
}