<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\NLU;

use Commune\Blueprint\Ghost\Mindset;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;


/**
 * 自然语言理解单元.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface NLUService
{
    /**
     * 可以被NLU 单元处理的消息.通常是文本消息.
     * @param HostMsg $message
     * @return bool
     */
    public function messageCouldHandle(HostMsg $message) : bool;

    /**
     * 尝试理解消息.
     * @param HostMsg $message
     * @param Comprehension $comprehension
     * @return Comprehension
     */
    public function Comprehend(
        HostMsg $message,
        Comprehension $comprehension
    ) : Comprehension;


    /**
     * 同步思维设定.
     * @param Mindset $mind
     * @return string
     */
    public function syncMindset(Mindset $mind) : string /* result */ ;


}