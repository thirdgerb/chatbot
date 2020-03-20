<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint;

use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Ghost\Blueprint\Session\History;
use Commune\Chatbot\Ghost\Blueprint\Session\SessionId;
use Commune\Chatbot\Ghost\Blueprint\Session\Tracker;
use Psr\Log\LoggerInterface;


/**
 * 会话相关的历史管理
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $chatId  所属 chat
 * @property-read string $sessionId 所属 SessionId
 * @property-read Conversation $conversation  本轮对话的容器
 * @property-read IncomingMessage $incomingMessage 输入信息
 * @property-read NLU $nlu 语义理解单元
 * @property-read History $history 对话历史
 * @property-read Tracker $tracker 上下文切换的追踪器
 * @property-read LoggerInterface $logger 日志模块
 */
interface Session
{

    /*------- status -------*/

    /**
     * 会话是否进行中
     * @return bool
     */
    public function isAvailable() : bool;


    /*------- operates -------*/

    /**
     * 结束本回合的会话. 保存数据, 回收垃圾.
     */
    public function finish() : void;

    /**
     * 退出整个会话.
     */
    public function quit() : void;

    /**
     * 遗忘会话的数据.
     */
    public function forget() : void;
}