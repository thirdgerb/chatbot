<?php

namespace Commune\Chatbot\Command;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Message;

class AnalyzerPipe extends CommandPipe
{
    protected function getCommandConfig(): array
    {
        $analyzers = $this->app->getConfig(ChatbotApp::RUNTIME_ANALYZERS, []);
        $commands = $this->app->getConfig(ChatbotApp::RUNTIME_USER_COMMANDS);
        return array_unique(array_merge($analyzers, $commands));
    }

    protected function getCommandMark(): string
    {
        return $this->app->getConfig(ChatbotApp::RUNTIME_ANALYZER_MARK, '/');
    }

    public function handle(Conversation $conversation, \Closure $next): Conversation
    {
        if (!$this->app->isSupervisor($conversation->getSender())) {
            return $next($conversation);
        }

        return parent::handle($conversation, $next);
    }

    protected function parseCommand(string $commandText, Message $message)
    {
        return new MsgCmdIntent($commandText, $message);
    }
}