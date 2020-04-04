<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Intercom;

use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;


/**
 * Ghost 内部通信使用的消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $chatId
 * @property-read string $shellName
 * @property-read string $messageId
 * @property-read string $traceId              链路追踪的ID
 * @property-read ShellMsg $shellMessage
 */
interface GhostMsg extends BabelSerializable, ArrayAndJsonAble
{
    /**
     * 根据当前消息的作用域, 衍生出不同发送渠道的消息
     *
     * @param Message $message
     * @param array $shellChatIds
     * @param int|null $deliverAt
     * @return GhostOutput[]
     */
    public function derive(
        Message $message,
        array $shellChatIds,
        int $deliverAt = null
    ) : array;

    /**
     * 替换当前消息的 Message
     * @param Message $message
     */
    public function replace(Message $message) : void;
}