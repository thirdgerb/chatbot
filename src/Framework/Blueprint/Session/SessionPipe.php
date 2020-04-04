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
 *
 */
interface SessionPipe
{
    const SYNC = 'sync';
    const ASYNC_INPUT = 'asyncInput';
    const ASYNC_OUTPUT = 'asyncOutput';

    public function stopPropagation() : void;

    public function isAsync() : bool;

    public function isAsyncInput() : bool;

    public function isAsyncOutput() : bool;

    public function sync(Session $session, callable $next) : Session;

    public function asyncInput(Session $session, callable $next) : Session;

    public function asyncOutput(Session $session, callable $next) : Session;


}