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
use Commune\Support\Protocal\ProtocalInstance;
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
     * 是否是调试模式.
     * @return bool
     */
    public function isDebugging() : bool;


    /**
     * @return string
     */
    public function getTraceId() : string;


    /**
     * Session 的名称. 如果一个应用有多个 Session, 考虑到缓存等, 可以做区别.
     * @return string
     */
    public function getName() : string;

    /**
     * SessionId 并不是
     * @return string
     */
    public function getSessionId() : string;


    /**
     * @return SessionStorage
     */
    public function getStorage() : SessionStorage;

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

    /*----- run 运行逻辑 -----*/

    /**
     * 根据配置, 基于协议获取一个 Handler
     * 调用 $handler($request) : $response 可以得到结果.
     * 用这种策略避免去开发复杂的通用 Kernel, 而可以适用于各种情况.
     *
     * @param string $group         假设协议处理器是分组的.
     * @param ProtocalInstance $protocal
     * @return callable|null
     */
    public function getProtocalHandler(
        string $group,
        ProtocalInstance $protocal
    ) : ? callable ;


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


    /*----- 锁 -----*/

    /**
     * 锁定一个机器人的分身. 禁止通讯.
     *
     * @param int $second
     * @return bool
     */
    public function lock(int $second) : bool;

    /**
     * @return bool
     */
    public function isLocked() : bool;

    /**
     * 解锁一个机器人的分身. 允许通讯.
     * @return bool
     */
    public function unlock() : bool;


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
     * Session 缓存的过期时间. 为 0 表示不限时间.
     * @return int
     */
    public function getSessionExpire() : int;

    /**
     * 设置当前 session 的过期时间, 可用来更改 session 默认的续期.
     * @param int $seconds     0 表示立刻 expire, -1 表示永久.
     */
    public function setSessionExpire(int $seconds) : void;

    /*------ logger ------*/

    public function getLogger() : LoggerInterface;

    /*------ finish ------*/

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;

    /**
     * @return bool
     */
    public function isFinished() : bool;

}