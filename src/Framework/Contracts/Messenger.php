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

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\Intercom\GhostMsg;
use Commune\Framework\Blueprint\Intercom\GhostOutput;

/**
 * Shell 和 Ghost 进行通讯的桥梁.
 * 多个 Shell 和 Ghost 共用这个桥梁.
 * 这个桥梁应该是一个通用的方案, 而不是每个 Shell 自行维护一套.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Messenger
{
    /*-------- 异步的输入消息 ---------*/

    /**
     * 缓冲一个请求数据.
     * 如果是全异步的通讯, 可能需要这个通道
     *
     * @param GhostInput $message
     * @param bool $atHead
     */
    public function pushInput(GhostInput $message, bool $atHead = true) : void;

    /**
     * 如果是全异步的通讯, 可能需要用这个管道来获取新的消息
     *
     * @return GhostInput
     */
    public function popInput() : ? GhostInput;

    /**
     * 同步发送一个标准的请求给 Ghost, 拿到同步的响应.
     *
     * @param GhostInput $message
     * @return bool
     */
    public function sendInput(GhostInput $message) : bool;

    /*-------- 异步输入消息 ---------*/


    /**
     * 广播多条消息给各个接受方.
     *
     * @param GhostOutput[] $messages
     * @return int
     */
    public function sendOutputs(array $messages) : int;


    /**
     * 取出一个 Shell + Chat 的所有异步消息.
     *
     * Shell 在同步请求中取, 可以用来发送同步响应
     * Shell 在双工服务端可以遍历, 获取所有 connection 对应的消息
     *
     * @param string $shellName
     * @param string $shellChatId
     * @return GhostOutput[]
     */
    public function fetchOutputs(
        string $shellName,
        string $shellChatId
    ) : array;

    /*-------- 订阅  ---------*/

    /**
     * 监听任何一条消息
     * 当 Shell 可以主动推送任何 Chat 消息时, 可以通过这种方式来获取
     *
     * @param string $shellName
     * @return GhostOutput|null
     */
    public function popOutput(string $shellName) : ? GhostOutput;


    /*-------- 记录消息 ---------*/

    /**
     * @param GhostMsg[] $messages
     */
    public function recordMessage(array $messages) : void;
}