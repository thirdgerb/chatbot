<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Message;

use Commune\Protocals\MessageProto;

/**
 * 对话类型的消息.
 * 对 Ghost 可能引起多轮对话状态的变动, 对 Client 则产生对话内容的展示等.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface ConvoProto extends MessageProto
{
    // 只有能渲染才发送的消息
    const DEBUG = 'debug';

    // 正常的消息
    const INFO = 'info';
}