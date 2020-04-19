<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Pipeline;

use Commune\Framework\Blueprint\Session;
use Commune\Framework\Prototype\Session\ASessionPipe;
use Commune\Ghost\Blueprint\Convo\Conversation;

/**
 * 异步锁. 如果没锁上, 重新入队.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AsyncChatLockerPipe extends ASessionPipe
{
    protected $locked = false;

    /**
     * @param Conversation $session
     * @param callable $next
     * @return Conversation
     * @throws \Throwable
     */
    protected function next(Session $session, callable $next): Session
    {
        try {

            return parent::next($session, $next);

        } catch (\Throwable $e) {

            if ($this->locked) {
                $session->cloner->unlock();
            }

            throw $e;
        }
    }


    /**
     * @param Conversation $session
     * @return Conversation
     */
    protected function before($session)
    {
        $chat = $session->cloner;
        $this->locked = $chat->lock();

        if ($this->locked) {
            return $session;
        }

        $session->messenger->pushInput($session->ghostInput);
        $session->finish();
        return $session;
    }

    /**
     * @param Conversation $session
     * @return Conversation
     */
    protected function after($session)
    {
        if ($this->locked) {
            $session->cloner->unlock();
        }
        return $session;
    }


}