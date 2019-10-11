<?php


namespace Commune\Chatbot\OOHost\NLU\Pipe;

use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

class NLUServicePipe implements SessionPipe
{
    /**
     * @var NLUService
     */
    protected $nluService;

    /**
     * @var NLULogger
     */
    protected $nluLogger;

    /**
     * NLUServicePipe constructor.
     * @param NLUService $nluService
     * @param NLULogger $nluLogger
     */
    public function __construct(NLUService $nluService, NLULogger $nluLogger)
    {
        $this->nluService = $nluService;
        $this->nluLogger = $nluLogger;
    }


    public function handle(Session $session, \Closure $next): Session
    {
        // 只匹配一次.
        $handled = $session->nlu->isHandledBy();
        $matched = $session->nlu->getMatchedIntent();
        if (isset($handled) || isset($matched)) {
            $this->nluLogger->logNLUResult($session);
            return $next($session);
        }


        // 消息类型不需要匹配.
        if (!$this->nluService->messageCouldHandle($session->incomingMessage->getMessage())) {
            return $next($session);
        }

        $session = $this->nluService->match($session);
        $this->nluLogger->logNLUResult($session);
        return $next($session);
    }

}