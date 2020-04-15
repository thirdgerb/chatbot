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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Runtime
{
    /*------ process -------*/

    public function getProcess() : Process;

    /*------ context -------*/

    public function newContext(string $contextName, array $entities = null) : Context;

    public function findContext(string $contextId) : ? Context;

    public function cacheContext(Context $context) : void;

    /*------ yielding -------*/

    public function addYielding(Thread $thread) : void;

    public function findYielding(string $threadId) : ? Thread;

    /*------ recollection -------*/


    /*------ save -------*/

    public function save(Session $session) : void;

}