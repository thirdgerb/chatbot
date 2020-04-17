<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;

use Commune\Framework\Blueprint\Session\Session;
use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Operator\Operation;
use Commune\Message\Blueprint\ContextMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Trace $trace
 * @property-read Route $route
 */
interface Runtime
{
    /*------ trace -------*/

    public function recordRoute(Node $node) : void;

    /*------ process -------*/

    public function getCurrentProcess() : Process;

    public function setCurrentProcess(Process $process) : void;

    public function findProcess(string $processId) : ? Process;

    public function expireProcess(string $processId) : void;

    /*------ context -------*/

    public function toContextMsg() : ? ContextMsg;

    public function findContext(string $contextId) : ? Context;

    public function cacheContext(Context $context) : void;

    /*------ yielding -------*/

    public function setYielding(Thread $thread, int $ttl = null) : void;

    public function findYielding(string $threadId) : ? Thread;

    /*------ recollection -------*/


    /*------ save -------*/

    public function save(Session $session) : void;

}