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
use Commune\Ghost\Blueprint\Session\GhtSession;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DuplexChatLockerPipe extends ASessionPipe
{
    protected $locked = false;

    /**
     * @param GhtSession $session
     * @param callable $next
     * @return GhtSession
     * @throws \Throwable
     */
    public function handle(Session $session, callable $next): Session
    {
        try {

            return parent::handle($session, $next);

        } catch (\Throwable $e) {
            if ($this->locked) {
                $session->chat->unlock();
            }

            throw $e;
        }
    }

    /**
     * @param GhtSession $session
     * @return GhtSession
     */
    protected function before($session)
    {
        $chat = $session->chat;
        $this->locked = $chat->lock();

        if ($this->locked) {
            return $session;
        }

        // 如果锁住了, 就将输入消息重新入队.
        $session->messenger->pushInput($session->ghostInput, false);
        $session->finish();
        return $session;
    }

    /**
     * @param GhtSession $session
     * @return GhtSession
     */
    protected function after($session)
    {
        if ($this->locked) {
            $session->chat->unlock();
        }
        return $session;
    }


}