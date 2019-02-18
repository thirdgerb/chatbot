<?php

/**
 * Class UserCommandPipe
 * @package Commune\Chatbot\Command
 */

namespace Commune\Chatbot\Command;

use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Message;


class UserCommandPipe extends CommandPipe
{
    protected $nextWithCommand = true;

    protected function getCommandConfig(): array
    {
        return $this->app->getConfig(ChatbotApp::RUNTIME_USER_COMMANDS, []);
    }

    protected function getCommandMark(): string
    {
        return $this->app->getConfig(ChatbotApp::RUNTIME_USER_COMMAND_MARK, '/');
    }

    protected function parseCommand(string $commandText, Message $message)
    {
        return new MsgCmdIntent($commandText, $message);
    }


}