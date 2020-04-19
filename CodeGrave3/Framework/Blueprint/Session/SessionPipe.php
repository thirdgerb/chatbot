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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SessionPipe
{
    const SYNC = 'sync';
    const ASYNC_INPUT = 'asyncInput';
    const ASYNC_OUTPUT = 'asyncOutput';

    /**
     * 停止管道继续往后走.
     */
    public function stopPropagation() : void;

    /*----- 反应当前管道的状态 -----*/

    /**
     * 是否是异步请求
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * 是否是异步输入请求 (无输出)
     * @return bool
     */
    public function isAsyncInput() : bool;

    /**
     * 是否是异步输出请求 (无输入)
     * @return bool
     */
    public function isAsyncOutput() : bool;

    /*----- 管道方法 -----*/

    /**
     * 同步请求
     * @param Session $session
     * @param callable $next
     * @return Session
     */
    public function sync(Session $session, callable $next) : Session;

    /**
     * 异步输入请求
     * @param Session $session
     * @param callable $next
     * @return Session
     */
    public function asyncInput(Session $session, callable $next) : Session;

    /**
     * 异步输出请求
     * @param Session $session
     * @param callable $next
     * @return Session
     */
    public function asyncOutput(Session $session, callable $next) : Session;


}