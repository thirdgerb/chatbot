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

use Commune\Framework\Prototype\Session\ASessionPipe;
use Commune\Ghost\Blueprint\Chat\Chat;
use Commune\Ghost\Blueprint\Session\GhtSession;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostMessengerPipe extends ASessionPipe
{
    /**
     * @param GhtSession $session
     * @return GhtSession
     */
    protected function before($session)
    {
        return $session;
    }


    /**
     * @param GhtSession $session
     * @return GhtSession
     */
    protected function after($session)
    {
        // 试图传输消息.
        $chat = $session->chat;
        $delivery = [];

        // 广播 input
        $delivery = $this->broadcastInput($delivery, $session, $chat);

        // 广播输出消息
        $outputs = $session->getOutputs();
        if (!empty($outputs)) {
            foreach ($outputs as $output) {
                $delivery[$output->shellName][] = $output;
            }
        }

        // 看看是否要广播
        if (empty($delivery)) {
            return $session;
        }

        // 进行广播
        $messenger = $session->messenger;
        foreach ($delivery as $shellName => $outputs) {
            $messenger->sendOutputs($outputs);
        }

        return $session;
    }

    protected function broadcastInput(array $delivery, GhtSession $session, Chat $chat) : array
    {
        $input = $session->ghostInput;
        $shells = $chat->scope->shells;
        $inputBroadcasts = $input->derive(
            $input->shellMessage->message,
            $shells
        );

        foreach ($inputBroadcasts as $output) {

            // 不需要广播到自己身上.
            if ($output->shellName === $input->shellName) {
                continue;
            }

            $delivery[$output->shellName][] = $output;
        }

        return $delivery;
    }


}