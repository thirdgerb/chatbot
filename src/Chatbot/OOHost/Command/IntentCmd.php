<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\Transformed\CommandMsg;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
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
     * @var RootIntentRegistrar
     */
    protected $repo;

    /**
     * IntentCmd constructor.
     * @param string $intentName
     * @param RootIntentRegistrar $repo
     */
    public function __construct(string $intentName, RootIntentRegistrar $repo)
    {
        $this->intentName = $intentName;
        $this->repo = $repo;
    }

    protected function getRepo() : RootIntentRegistrar
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


    public function handle(CommandMsg $message, Session $session, SessionCommandPipe $pipe): void
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