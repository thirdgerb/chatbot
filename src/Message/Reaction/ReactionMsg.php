<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Reaction;

use Carbon\Carbon;
use Commune\Message\Message;

/**
 * 从 Ghost 发出的消息. 需要在 Shell 上渲染成 ConvoMsg 得以发送给客户端.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ReactionMsg extends Message
{

    /**
     * ReactionId
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Slots
     *
     * @return array
     */
    public function getSlots() : array;

    /**
     * 预计发送时间. 为 null 表示随时.
     * @return Carbon|null
     */
    public function getDeliverAt() : ? Carbon;
}