<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Session;

use Commune\Framework\Blueprint\App;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Server\Server;
use Commune\Framework\Contracts\Cache;
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Session
{
    /*------ properties ------*/


    /**
     * Session 在所有进程的唯一ID
     * @return string
     */
    public function getUuId() : string;


    /**
     * Session 所处的 Chat Id
     * @return string
     */
    public function getChatId() : string;


    /**
     * @return string
     */
    public function getSessionId() : string;

    /**
     * 当前请求的场景 ID
     * @return string
     */
    public function getSceneId() : string;

    /**
     * @param string $name
     * @param $object
     */
    public function setProperty(string $name, $object): void;

    /**
     * Session 缓存的过期时间.
     * @return int
     */
    public function getSessionExpire() : int;

    /*------ input ------*/

    /**
     * @return Server
     */
    public function getServer() : Server;

    /**
     * @return Request
     */
    public function getRequest() : Request;

    /**
     * @return Response
     */
    public function getResponse() : Response;


    /*------ component ------*/

    /**
     * @return App
     */
    public function getApp() : App;

    /**
     * @return ReqContainer
     */
    public function getContainer() : ReqContainer;

    /**
     * @return SessionStorage
     */
    public function getStorage() : SessionStorage;

    /**
     * @return SessionLogger
     */
    public function getLogger() : SessionLogger;

    /**
     * @return Cache
     */
    public function getCache() : Cache;

    /*------ status save ------*/

    /**
     * 设置为无状态请求
     */
    public function noState() : void;

    /**
     * 重置 session 信息.
     */
    public function reset() : void;

    /**
     * 是否是无状态的 session
     * @return bool
     */
    public function isStateless() : bool;

    /*------ output ------*/

    /**
     * 回复单条 message
     * @param Message $message
     */
    public function output(Message $message) : void;

    /*------ finish ------*/

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;

    /**
     * @return bool
     */
    public function isFinished() : bool;

    /*------ event ------*/

    /**
     * 触发一个 Session 事件.
     * @param SessionEvent $event
     */
    public function fire(SessionEvent $event) : void;

    /**
     * @param string $eventName
     * @param callable $handler function(Session $session, SessionEvent $event){}
     */
    public function listen(string $eventName, callable $handler) : void;

}