<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\Blueprint\Message\Event\Connect;
use Commune\Chatbot\Blueprint\Message\Event\EndSession;
use Commune\Chatbot\Blueprint\Message\EventMsg;
use Commune\Chatbot\Blueprint\Message\Event\StartSession;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

/**
 * 全局的处理事件消息的拦截器
 * 根据事件做额外的处理逻辑.
 */
class EventMsgPipe implements SessionPipe
{
    public function handle(Session $session, \Closure $next): Session
    {
        $message = $session->incomingMessage->getMessage();

        if (!$message instanceof EventMsg) {
            return $next($session);
        }

        $navigator = $this->handleEvent($message, $session);
        if (!isset($navigator)) {
            return $next($session);
        }

        $session->handle(
            $message,
            $navigator
        );

        return $session;
    }

    protected function handleEvent(EventMsg $message, Session $session) : ? Navigator
    {
        if ($message instanceof Connect) {
            return $session->dialog->repeat();
        }

        if ($message instanceof StartSession) {
            return $session->dialog->redirect->home();
        }

        if ($message instanceof EndSession) {
            return $session->dialog->quit();
        }

        return null;
    }

}