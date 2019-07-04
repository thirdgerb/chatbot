<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\Registrar;
use Commune\Chatbot\OOHost\Session\Session;

class IntentCmd extends SessionCommand
{
    /**
     * @var string
     */
    protected $intentName;

    protected $sneak = false;

    /**
     * @var string
     */
    protected $commandName;

    /**
     * IntentCmd constructor.
     * @param string $intentName
     */
    public function __construct(string $intentName)
    {
        $this->intentName = $intentName;
    }

    protected function getRepo() : Registrar
    {
        return IntentRegistrar::getIns();
    }

    public function getCommandName(): string
    {
        return $this->commandName
            ?? $this->commandName = $this->getRepo()
            ->getMatcher($this->intentName)
            ->getCommand()
            ->getCommandName();
    }

    public function getCommandDefinition(): CommandDefinition
    {
        return $this->getRepo()
            ->getMatcher($this->intentName)
            ->getCommand();
    }

    public function getDescription(): string
    {
        return $this->getRepo()
            ->get($this->intentName)
            ->getDesc();
    }


    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        /**
         * @var IntentMessage $intent
         */
        $intent = $this->getRepo()
            ->get($this->intentName)
            ->newContext($message->getEntities());

        $navigator = $intent->navigate($session->dialog);
        $session->hear(
            $session->incomingMessage->message,
            $navigator
        );
    }


}