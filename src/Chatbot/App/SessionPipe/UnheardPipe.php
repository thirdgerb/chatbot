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

        if ($session->shouldQuit()) {
            $farewell = $session
                ->chatbotConfig
                ->defaultMessages
                ->farewell;
            $session->dialog->say()->info($farewell);
            return $session;
        }

        if (!$session->isHeard()) {

            $missMatched = $session
                ->chatbotConfig
                ->defaultMessages
                ->messageMissMatched;
            $session->dialog->say()->warning($missMatched);

            return $session;
        }

        return $session;
    }


}