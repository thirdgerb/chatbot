<?php


namespace Commune\Chatbot\App\SessionPipe;


use Commune\Chatbot\Blueprint\Message\Event\EventMsg;
use Commune\Chatbot\Framework\Messages\Events\ConnectionEvt;
use Commune\Chatbot\Framework\Messages\Events\QuitEvt;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

/**
 * 全局的处理事件消息的拦截器
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

        $session->hear(
            $message,
            $navigator
        );

        return $session;
    }

    protected function handleEvent(EventMsg $message, Session $session) : ? Navigator
    {
        // 连接事件, 重启当前会话.
        switch ($message->getEventName()) {
            case ConnectionEvt::class:
                return $session->dialog->repeat();
            case QuitEvt::class:
                return $session->dialog->quit();


            default:
                return null;
        }


    }

}