<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

class MarkedIntentPipe implements SessionPipe
{
    public function handle(Session $session, \Closure $next): Session
    {
        // debug 模式才允许用.
        if (!CHATBOT_DEBUG) {
            return $next($session);
        }

        $intent = $session->nlu->getMatchedIntent();
        if (isset($intent)) {
            return $next($session);
        }

        $message = $session->incomingMessage->message;
        // only verbose
        if (!$message instanceof VerbalMsg) {
            return $next($session);
        }

        $text = $message->getTrimmedText();

        if (!preg_match('/^#[\w\.:]+#$/', $text)) {
            return $next($session);
        }

        $intentName = trim($text, '#');
        if (!empty($intentName)) {
            $session->nlu->setMatchedIntent($intentName);
        }
        return $next($session);
    }


}