<?php


namespace Commune\Chatbot\OOHost\NLU;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

/**
 * 作为 pipeline 的NLU
 */
abstract class NLUSessionPipe implements SessionPipe, NatureLanguageUnit
{

    public function handle(Session $session, \Closure $next): Session
    {
        $message = $session->incomingMessage->message;
        if (!$this->messageCouldHandle($message)) {
            return $next($session);
        }


        $matches = $this->match($session);

        if (!empty($matches)) {
            $incomingMessage = $session->incomingMessage;
            // 将结果赋值.
            $session->incomingMessage = $matches->applyToIncomingMessage($incomingMessage);
        }


        /**
         * @var Session $session
         */
        $session = $next($session);

        // log unmatched
        if (empty($allMatched) && !$session->isHeard()) {
            $this->logUnmatchedMessage($session);
        }

        return $session;
    }


}