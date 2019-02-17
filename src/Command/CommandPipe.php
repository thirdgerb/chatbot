<?php

/**
 * Class CommandPIpe
 * @package Commune\Chatbot\Command
 */

namespace Commune\Chatbot\Command;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\ChatbotPipe;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Support\ChatbotUtils;

abstract class CommandPipe implements ChatbotPipe
{
    /**
     * @var ChatbotApp
     */
    protected $app;

    protected $commands;

    protected $commandMark;

    protected $nextWithCommand = false;

    public function __construct(ChatbotApp $app)
    {
        $this->app = $app;
        $this->bootstrap();
    }

    protected function bootstrap()
    {
        $commands = $this->getCommandConfig();

        foreach ($commands as $commandName) {

            $command = $this->app->make($commandName);
            if (!$command instanceof Command) {
                //todo
                throw new ConfigureException();
            }
            $this->commands[] = $command;
        }

        $this->commandMark = $this->getCommandMark();
    }

    abstract protected function getCommandConfig() : array;

    abstract protected function getCommandMark() : string;


    public function handle(Conversation $conversation, \Closure $next): Conversation
    {
        $text = $conversation->getMessage()->getTrimText();
        $commandText = ChatbotUtils::getCommandStr($text, $this->commandMark);

        if (!$commandText) {
            return $next($conversation);
        }

        $messageCommand = new MsgCmdIntent($commandText, $conversation->getMessage());
        if ($this->nextWithCommand) {
            $conversation->setCommandIntent($messageCommand);
        }

        if (empty($this->commands)) {
            return $next($conversation);
        }

        foreach ($this->commands as $command) {
            /**
             * @var Command $command
             */
            if ($command->match($messageCommand)) {
                return $command->handle($messageCommand, $conversation);
            }
        }


        $returnr = $next($conversation);
        return $returnr;
    }


}