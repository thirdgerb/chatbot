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

use Commune\Framework\Blueprint\Abstracted\Comprehension;
use Commune\Message\Blueprint\IntentMsg;
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $messageId             消息的唯一ID
 * @property-read string $chatId                对话域的唯一ID
 * @property-read string $shellName             平台的名称
 * @property-read string $traceId               链路追踪的ID
 * @property-read ShellInput $shellMessage      平台输入消息
 * @property-read bool $stateless               是否无状态请求
 *
 * @property-read string $sceneId               请求场景信息
 * @property-read array $sceneEnv               请求场景变量
 * @property-read Comprehension $comprehension  对请求的抽象理解
 *
 */
interface GhostInput extends GhostMsg
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
    public function reply(Message $message, int $deliverAt = null) : GhostOutput;
}