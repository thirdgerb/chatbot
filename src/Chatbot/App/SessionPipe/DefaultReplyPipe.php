<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

/**
 * 根据session 的状态, 预定义默认回复.
 */
class DefaultReplyPipe implements SessionPipe
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