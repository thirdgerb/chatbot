<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

/**
 * @deprecated  方案变更, 不需要这个环节了.
 *
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
            $session->conversation->getSpeech()->info($farewell);
            return $session;
        }

        if (!$session->isHeard()) {

            $missMatched = $session
                ->chatbotConfig
                ->defaultMessages
                ->messageMissMatched;
            $session->conversation->getSpeech()->warning($missMatched);

            return $session;
        }

        return $session;
    }


}