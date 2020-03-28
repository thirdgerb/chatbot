<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Reaction;

use Carbon\Carbon;
use Commune\Message\Blueprint\Message;
use Commune\Message\Blueprint\Tag\Level;

/**
 * 从 Ghost 发出的消息. 需要在 Shell 上渲染成 ConvoMsg 得以发送给客户端.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ReactionMsg extends Message, Level
{
    // 默认的消息
    const FALWELL = 'reaction.falwell';

    const BLOCKED = 'reaction.block';
    # 默认的
    const CONFUSE = 'reaction.confuse';
    # 默认的拒绝消息
    const REJECT = 'reaction.reject';

    # 文字消息的前缀. 通常需要翻译.
    const MESSAGE_PREFIX = 'message';

    /**
     * ReactionId
     *
     * @return string
     */
    public function getId() : string;

    /**
     * @return array
     */
    public function getPayload() : array;

    /**
     * 预计发送时间. 为 null 表示随时.
     * @return Carbon|null
     */
    public function getDeliverAt() : ? Carbon;
}