<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

class UnheardPipe implements SessionPipe
{
    public function handle(Session $session, \Closure $next): Session
    {
        /**
         * @var Session $session
         */
        $session = $next($session);

        if ($session->isQuiting()) {
            $farewell = $session
                ->chatbotConfig
                ->defaultMessages
                ->farewell;
            $session->conversation->monolog()->info($farewell);
            return $session;
        }

        if (!$session->isHeard()) {

            $missMatched = $session
                ->chatbotConfig
                ->defaultMessages
                ->messageMissMatched;
            $session->conversation->monolog()->warning($missMatched);

            return $session;
        }

        return $session;
    }


}