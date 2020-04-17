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

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Prototype\Session\ASessionPipe;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Message\Constants\SystemIntents;
use Commune\Message\Predefined\IIntentMsg;


/**
 * 同步锁, 用于锁定对话.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ChatLockerPipe extends ASessionPipe
{
    protected $locked = false;

    /**
     *
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
        $session->output(new IIntentMsg(SystemIntents::CHAT_BLOCKED));
        // 挡住了就不要继续了.
        $this->stopPropagation();
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