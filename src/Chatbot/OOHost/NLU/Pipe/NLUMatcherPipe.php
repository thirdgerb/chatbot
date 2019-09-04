<?php


namespace Commune\Chatbot\OOHost\NLU\Pipe;


use Commune\Chatbot\OOHost\NLU\Contracts\Manager;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

abstract class NLUMatcherPipe implements SessionPipe
{
    /**
     * @var Manager
     */
    protected $manager;

    public function handle(Session $session, \Closure $next): Session
    {
        $matched = $session->getMatchedIntent();
        if (isset($matched)) {
            return $next($session);
        }

        $nlu = $session->nlu;
        if ($nlu->isHandled()) {
            return $next($session);
        }

        $matched = $nlu->getMatchedIntent();
        if (isset($matched)) {
            return $next($session);
        }

        $matcher = $this->manager->getMatcher();
        if (!$matcher->messageCouldHandle($session->incomingMessage->getMessage())) {
            return $next($session);
        }

        $session = $matcher->match($session);
        $session = $next($session);

        return $this->returnSession($session);
    }

    abstract public function returnSession(Session $session) : Session;
}