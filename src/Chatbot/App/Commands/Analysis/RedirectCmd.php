<?php


namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;

class RedirectCmd extends SessionCommand
{
    const SIGNATURE = 'redirect
        {contextName : 导航到目标context}';


    const DESCRIPTION = '手动导航到一个目标context';

    protected $sneak = false;

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        $name = $message['contextName'];

        if (empty($name)) {
            $this->say()->error("command.invalidArgument", [
                'name' => 'contextName'
            ]);
            return;
        }

        $repo = $session->contextRepo;

        if (!$repo->hasDef($name)) {
            $this->say($message->getEntities())
                ->info('command.contextNotExists');
            return;
        }

        $this->say($message->getEntities())
            ->info('command.navigateToContext');

        $context = $repo->getDef($name)->newContext();
        $dialog = $session->dialog;

        if ($context instanceof IntentMessage) {
            $navigator = $context->navigate($dialog);
        } else {
            $navigator = $dialog->redirect->sleepTo($context);
        }

        $session->handle(
            $session->incomingMessage->message,
            $navigator
        );

        return;
    }


}