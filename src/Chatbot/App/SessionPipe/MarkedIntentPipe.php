<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;
use Illuminate\Support\Collection;

class MarkedIntentPipe implements SessionPipe
{
    public function handle(Session $session, \Closure $next): Session
    {
        $intent = $session->getMatchedIntent();
        if (isset($intent)) {
            return $next($session);
        }

        $message = $session->incomingMessage->message;
        // only verbose
        if (!$message instanceof VerboseMsg) {
            return $next($session);
        }

        $text = $message->getTrimmedText();

        if (!preg_match('/^#[\w\.:]+#$/', $text)) {
            return $next($session);
        }

        $intentName = trim($text, '#');
        if ($session->intentRepo->hasDef($intentName)) {
            $session->nlu->setMatchedIntent($intentName);
        }

        return $next($session);
    }


}