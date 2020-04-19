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

use Commune\Framework\Blueprint\Comprehension;
use Commune\Message\Blueprint\IntentMsg;
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $traceId               链路 ID
 * @property-read string|null $cloneId          消息所属的机器人分身ID
 * @property-read ShellInput $shellMessage      平台输入消息
 *
 * # 对话理解
 *
 * @property Comprehension $comprehension  对请求的抽象理解
 *
 */
interface GhostInput extends GhostMessage
{

    /**
     * 输入消息的文字表达
     * @return string
     */
    public function getTrimmedText() : string;

    /**
     * 是否存在命中意图
     * @return IntentMsg|null
     */
    public function getMatchedIntent() : ? IntentMsg;

    /**
     * 从输入消息衍生出输出消息.
     * @param Message $message
     * @param int|null $deliverAt
     * @return GhostOutput
     */
    public function output(Message $message, int $deliverAt = null) : GhostOutput;
}