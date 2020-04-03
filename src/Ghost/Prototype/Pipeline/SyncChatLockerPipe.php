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
use Commune\Message\Constants\OutgoingIntents;
use Commune\Message\Prototype\IIntentMsg;


/**
 * 同步锁, 用于锁定对话.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SyncChatLockerPipe extends ASessionPipe
{
    protected $locked = false;

    /**
     *
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

        // 锁定失败, 则发送同步消息, 并且不需要通知其它 shell
        $output = $session
            ->ghostInput
            ->reply(new IIntentMsg(OutgoingIntents::CHAT_BLOCKED));

        $session->messenger->sendOutputs([$output]);
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