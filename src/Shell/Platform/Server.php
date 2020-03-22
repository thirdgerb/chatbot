<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Platform;

use Commune\Message\Blueprint\ConvoMsg;
use Commune\Message\Blueprint\Message;
use Commune\Shell\Blueprint\Shell;

/**
 * 平台上的服务端实例.
 *
 * 同时负责主动推送和发送离线消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $platformName 平台名称
 * @property-read string $serverId 服务端实例的名称
 *
 * 可以依赖注入:
 *
 * @property-read Console $console
 */
interface Server
{
    /**
     * 运行实例
     */
    public function start() : void;

    /**
     * 获取 Shell 实例.
     * @return Shell
     */
    public function getShell() : Shell;


    /*---------- 特殊操作 ----------*/

    /**
     * 非阻塞地休眠. 如果可以做到的话.
     * @param float $seconds
     */
    public function coSleep(float $seconds) : void;

    /**
     * 尝试关闭服务端实例. 根据服务端情况.
     */
    public function shutdown() : void;

    /**
     * 重启服务端实例. 办得到吗?
     */
    public function reboot() : void;

    /*---------- 异步离线通讯 ------------*/

    /**
     * 是否可以离线发送. 不能主动推送时, 考虑离线推送.
     * @return bool
     */
    public function isOfflineSendAble() : bool;

    /**
     * 离线发送消息.
     *
     * @param string $chatId
     * @param Message $message
     * @return bool
     */
    public function sendOffline(string $chatId, Message $message) : bool;

    /**
     * 根据 ChatId 创建一个 Response
     * @param string $chatId
     * @return Response
     */
    public function makeResponse(string $chatId) : Response;

    /*---------- 双工通讯 ------------*/

    /**
     * 是否双工, 如果双工, 优先主动推送.
     * @return bool
     */
    public function isDuplex() : bool;

    /**
     * 双工通道是否建立, 如果通道建立, 则可以主动推送
     *
     * @param string $chatId
     * @return bool
     */
    public function isEstablished(string $chatId) : bool;

    /**
     * 获取当前已经建立的双工连接. channelIds
     * @return string[]
     */
    public function getEstablishedChats() : array;

    /**
     * 主动推送消息给 channel
     *
     * @param string $chatId
     * @param ConvoMsg $message
     * @return bool
     */
    public function send(string $chatId, ConvoMsg $message) : bool;


    /*---------- 循环发送.  ------------*/

    /**
     * 循环进行双工发送.
     *
     * 注意处理异常.
     *
     * @param callable $duplexSending
     */
    public function loopDuplexSending(callable $duplexSending) : void;

    /**
     * 发布一个任务, 用于发送单个 Chat的消息.
     *
     * 注意处理异常.
     *
     * @param callable $chatSending
     */
    public function addChatSendingTask(callable $chatSending) : void;

    /**
     * 循环进行离线发送.
     *
     * 注意处理异常.
     *
     * @param callable $sending
     */
    public function loopOfflineSending(callable $sending) : void;

}