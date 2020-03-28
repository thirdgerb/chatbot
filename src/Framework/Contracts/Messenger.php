<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Contracts;

use Commune\Chatbot\Exceptions\MessengerReqException;
use Commune\Message\Blueprint\Internal\InputMsg;
use Commune\Message\Blueprint\Internal\OutputMsg;

/**
 * Shell 和 Ghost 进行通讯的桥梁.
 * 多个 Shell 和 Ghost 共用这个桥梁.
 * 这个桥梁应该是一个通用的方案, 而不是每个 Shell 自行维护一套.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Messenger
{

    /**
     * 缓冲一个请求数据.
     * 如果是全异步的通讯, 可能需要这个通道
     *
     * @param string $chatId
     * @param string $shellId
     * @param InputMsg $message
     * @return mixed
     */
    public function bufferIncoming(
        string $chatId,
        string $shellId,
        InputMsg $message
    );

    /**
     * 如果是全异步的通讯, 可能需要用这个管道来获取新的消息
     *
     * @param string $chatId
     * @param string $shellId
     * @return mixed
     */
    public function popIncoming(
        string $chatId,
        string $shellId
    ) : InputMsg;


    /**
     * 同步发送一个标准的请求给 Ghost, 拿到同步的响应.
     *
     * @param InputMsg $message 同步请求
     * @return OutputMsg[] 同步响应.
     * @throws MessengerReqException 抛出请求异常.
     */
    public function syncSendIncoming(InputMsg $message) : array;

    /**
     * 广播多条消息给各个接受方. 接受方由 Scope 来决定.
     * 消息应该进入 shell 的队列, 同时广播 (publish) 出去.
     *
     * @param OutputMsg[] $messages
     * @return int
     */
    public function asyncSendOutgoing(array $messages) : int;

    /**
     * 取出一个 Shell + Chat 的所有异步消息.
     *
     * Shell 在同步请求中取, 可以用来发送同步响应
     * Shell 在双工服务端可以遍历, 获取所有 connection 对应的消息
     *
     * @param string $chatId
     * @param string $shellName
     * @return OutputMsg[]
     */
    public function fetchShellOutgoing(string $chatId, string $shellName) : array;

    /**
     * 监听任何一条消息
     * 当 Shell 可以主动推送任何 Chat 消息时, 可以通过这种方式来获取
     *
     * 注意监听不是队列, 不要用多个进程去监听, 不然逻辑就重复了.
     *
     * @param string $shellName
     * @return OutputMsg
     */
    public function subscribe(string $shellName) : OutputMsg;

    /*-------- 消息存储 ---------*/

    /**
     * 记录一个输入消息. 不负责查询.
     * @param InputMsg $message
     * @param bool $confused
     */
    public function recordIncoming(
        InputMsg $message,
        bool $confused
    ) : void;

    /**
     * 记录一个输出消息. 不负责查询.
     * @param OutputMsg $message
     * @param string $incomingId
     */
    public function recordOutgoing(
        OutputMsg $message,
        string $incomingId
    ) : void;
}