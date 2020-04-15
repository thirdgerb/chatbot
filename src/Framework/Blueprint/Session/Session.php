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
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Session
{
    public function getUuid() : string;

    public function getSessionId() : string;

    /**
     * 是否是调试模式.
     * @return bool
     */
    public function isDebugging() : bool;

    /*------ expire ------*/


    /**
     * 设置为无状态请求
     */
    public function noState() : void;

    /**
     * 是否是无状态的 session
     * @return bool
     */
    public function isStateless() : bool;


    /**
     * Session 缓存的过期时间.
     * @return int
     */
    public function getSessionExpire() : int;

    /**
     * 设置当前 session 的过期时间, 可用来更改 session 默认的续期.
     * @param int $int
     */
    public function setSessionExpire(int $int) : void;

    /*------ request ------*/

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

    /*------ pipe ------*/

    /**
     * @param array $pipes
     * @param string $via
     * @return static
     */
    public function goThroughPipes(array $pipes, string $via);

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