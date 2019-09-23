<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 用 command 来代理 intent 的调用.
 * 允许像使用 session command 一样使用 intent
 */
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
     * @var IntentRegistrar
     */
    protected $repo;

    /**
     * IntentCmd constructor.
     * @param string $intentName
     * @param IntentRegistrar $repo
     */
    public function __construct(string $intentName, IntentRegistrar $repo)
    {
        $this->intentName = $intentName;
        $this->repo = $repo;
    }

    protected function getRepo() : IntentRegistrar
    {
        return $this->repo;
    }

    public function getCommandName(): string
    {
        return $this->commandName
            ?? $this->commandName = $this->getRepo()
            ->getDef($this->intentName)
            ->getMatcher()
            ->getCommand()
            ->getCommandName();
    }

    public function getCommandDefinition(): CommandDefinition
    {
        return $this->getRepo()
            ->getDef($this->intentName)
            ->getMatcher()
            ->getCommand();
    }

    public function getDescription(): string
    {
        return $this->getRepo()
            ->getDef($this->intentName)
            ->getDesc();
    }


    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        /**
         * @var IntentMessage $intent
         */
        $intent = $this->getRepo()
            ->getDef($this->intentName)
            ->newContext($message->getEntities());

        $navigator = $intent->navigate($session->dialog);
        $session->handle(
            $session->incomingMessage->message,
            $navigator
        );
    }


}