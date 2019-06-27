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
        foreach ($allMatched as $matched) {
            $session->incomingMessage->addPossibleIntent(
                $matched->name,
                $matched->entities,
                $matched->confidence
            );
        }

        /**
         * @var Session $session
         */
        $session = $next($session);

        // log unmatched
        $matched = $session->getMatchedIntent();
        if (empty($matched)) {
            $this->logUnmatchedMessage($session->incomingMessage);
        }

        return $session;
    }


}