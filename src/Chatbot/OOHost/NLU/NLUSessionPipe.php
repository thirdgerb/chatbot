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

        $allMatched = $this->matchIntents($message);
        $highlyPossible = [];
        $incomingMessage = $session->incomingMessage;
        foreach ($allMatched as $matched) {
            $incomingMessage->addPossibleIntent(
                $matched->name,
                $matched->entities,
                $matched->confidence
            );

            if ($matched->highlyPossible) {
                $highlyPossible[] = $matched->name;
            }
        }

        if (!empty($highlyPossible)) {
            $incomingMessage->setHighlyPossibleIntentNames($highlyPossible);
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