<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform;

use Commune\Messages\Blueprint\Message;
use Commune\Framework\Blueprint\Chat\ChatScope;
use Commune\Shell\Blueprint\Shell;

/**
 * 平台上的服务端实例.
 *
 * 同时负责主动推送和发送离线消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Server
{
    /**
     * 平台的 Id
     * @return string
     */
    public function getPlatformId() : string;

    /**
     * 运行实例
     */
    public function start() : void;

    /**
     * 获取 Shell 实例.
     * @return Shell
     */
    public function getShell() : Shell;

    /**
     * 是否双工, 如果双工, 优先主动推送.
     * @return bool
     */
    public function isDuplex() : bool;

    /**
     * 是否可以离线发送. 不能主动推送时, 考虑离线推送.
     * @return bool
     */
    public function isOfflineSendAble() : bool;

    /**
     * 离线发送消息.
     * @param ChatScope $chat
     * @param Message $message
     * @return bool
     */
    public function sendOffline(ChatScope $chat, Message $message) : bool;

    /**
     * 通道是否建立, 如果通道建立, 则可以主动推送
     * @param ChatScope $chat
     * @return bool
     */
    public function isEstablished(ChatScope $chat) : bool;

    /**
     * 获取当前已经建立的双工连接.
     * @return ChatScope[]
     */
    public function getConnections() : array;

    /**
     * 主动推送消息给 channel
     *
     * @param ChatScope $chat
     * @param Message $message
     * @return bool
     */
    public function send(ChatScope $chat, Message $message) : bool;


    /*---------- 获取属性 ------------*/


    /**
     * 实例的控制台输出
     * @return Console
     */
    public function getConsole() : Console;

}