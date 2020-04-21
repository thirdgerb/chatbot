<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework;

use Commune\Blueprint\Framework\Session\Event;


/**
 * 处理请求的 Session 对象. 存在于单次请求的生命周期中, 完成后销毁.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Session
{
    /**
     * @return string
     */
    public function getUuid() : string;

    /**
     * @return string
     */
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
     * @return ReqContainer
     */
    public function getContainer() : ReqContainer;

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
     * 生成一个管道.
     *
     * @param array $pipes
     * @param string $via
     * @return \Closure
     */
    public function buildPipeline(array $pipes, string $via) : \Closure;

    /*------ event ------*/

    /**
     * 触发一个 Session 事件.
     * @param Event $event
     */
    public function fire(Event $event) : void;

    /**
     * @param string $eventName
     * @param callable $handler function(Session $session, SessionEvent $event){}
     */
    public function listen(string $eventName, callable $handler) : void;



}