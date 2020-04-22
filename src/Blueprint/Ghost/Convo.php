<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Ghost;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\GhostMsg;

/**
 * 对话模块
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Convo
{

    /**
     * 用链式方式指定输出的消息
     *
     * @param array $slots  公共的 Slots
     * @return Convo\OutputBuilder
     */
    public function react(array $slots = []) : Ghost\Convo\OutputBuilder;

    /**
     * 发送任何类型的同步回复消息.
     * @param HostMsg $message
     * @return Convo
     */
    public function output(HostMsg $message) : Convo;

    /**
     * 投递输入消息到指定的 Clone
     *
     * @param GhostMsg $message
     * @return Convo
     */
    public function deliver(GhostMsg $message) : Convo;

}