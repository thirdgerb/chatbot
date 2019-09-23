<?php


namespace Commune\Chatbot\App\Commands\Navigation;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;

abstract class AbsNavigatorCmd extends SessionCommand
{
    const SIGNATURE = '';

    const DESCRIPTION = '';

    protected $sneak = false;

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        $session->handle(
            $session->incomingMessage->message,
            $this->navigate($session->dialog)
        );
    }

    abstract protected function navigate(Dialog $dialog) : Navigator;


}