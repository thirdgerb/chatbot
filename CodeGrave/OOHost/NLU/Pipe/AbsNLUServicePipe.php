<?php


namespace Commune\Chatbot\OOHost\NLU\Pipe;

use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

/**
 * 可以参考这种方式实现 nlu service pipe
 */
abstract class AbsNLUServicePipe implements SessionPipe
{

    abstract public function getNLUService() : NLUService;

    abstract public function getNLULogger() : NLULogger;

    public function handle(Session $session, \Closure $next): Session
    {
        // 只匹配一次.
        $handled = $session->nlu->isHandledBy();
        $matched = $session->nlu->getMatchedIntent();

        if (isset($handled) || isset($matched)) {
            return $next($session);
        }


        // 消息类型不需要匹配.
        $service = $this->getNLUService();
        if (!$service->messageCouldHandle($session->incomingMessage->getMessage())) {
            return $next($session);
        }

        $session = $service->match($session);
        $this->getNLULogger()->logNLUResult($session);
        return $next($session);
    }

}