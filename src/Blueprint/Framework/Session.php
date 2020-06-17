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

use Commune\Blueprint\Framework\Session\SessionEvent;
use Commune\Blueprint\Framework\Session\SessionStorage;
use Psr\Log\LoggerInterface;


/**
 * 处理请求的 Session 对象. 存在于单次请求的生命周期中, 完成后销毁.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Session
{

    /*----- properties -----*/

    /**
     * 每个 Session 实例都是在请求中生成的.
     * 因此每个实例拥有一个 traceId, 应该是全局唯一的ID.
     * 用这个 ID 来追踪上下文.
     *
     * @return string
     */
    public function getTraceId() : string;

    /**
     * sessionId 是会话的唯一标识.
     *
     * 用于追踪交互的历史记录.
     * 对于 1 对 1 会话, sessionId 对于用户是唯一的, 和用户相关
     * 对于 1 对多 会话, sessionId 则相当于群的 ID.
     *
     * @return string
     */
    public function getSessionId() : string;

    /**
     * Session 的名称. 如果一个应用有多个 Session, 考虑到缓存等, 可以做区别.
     * @return string
     */
    public function getAppId() : string;

    /*----- status -----*/

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
     * @return bool
     */
    public function isFinished() : bool;


    /*----- component -----*/

    /**
     * 获取容器
     * @return ReqContainer
     */
    public function getContainer() : ReqContainer;

    /**
     * 获取 App 自身.
     * @return App
     */
    public function getApp() : App;


    /**
     * @return SessionStorage
     */
    public function getStorage() : SessionStorage;


    /*------ logger ------*/

    public function getLogger() : LoggerInterface;


    /*------ pipe ------*/

    /**
     * 生成一个管道.
     *
     * @param array $pipes
     * @param string $via
     * @param \Closure $destination
     * @return \Closure
     */
    public function buildPipeline(array $pipes, string $via, \Closure $destination) : \Closure;

    /**
     * 触发一个 Session 事件.
     * @param SessionEvent $event
     */
    public function fire(SessionEvent $event) : void;

    /**
     * @param string $eventName
     * @param callable $handler function(Session $session, Event $event){}
     */
    public function listen(string $eventName, callable $handler) : void;



    /*------ expire ------*/


    /**
     * Session 缓存的过期时间. 为 0 表示不限时间.
     * @return int
     */
    public function getSessionExpire() : int;

    /**
     * 设置当前 session 的过期时间, 可用来更改 session 默认的续期.
     * @param int $seconds     0 表示立刻 expire, -1 表示永久.
     */
    public function setSessionExpire(int $seconds) : void;

    /*------ finish ------*/

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;

}