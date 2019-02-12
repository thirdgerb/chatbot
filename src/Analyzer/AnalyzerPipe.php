<?php

namespace Commune\Chatbot\Analyzer;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\ChatbotPipe;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\StringInput;

class AnalyzerPipe implements ChatbotPipe
{
    protected $app;

    protected $commands = [];

    protected $commandMark;

    public function __construct(ChatbotApp $app)
    {
        $this->app = $app;
        $this->bootstrap();
    }

    protected function bootstrap()
    {
        $commands = $this->app->getAnalyzerCommands();

        foreach ($commands as $commandName) {

            $command = $this->app->make($commandName);
            if (!$command instanceof AnalyzerCommand) {
                //todo
                throw new ConfigureException();
            }
            $this->commands[] = $command;
        }

        $this->commandMark = $this->app->getAnalyzerMark();
    }

    public function handle(Conversation $conversation, \Closure $next): Conversation
    {
        if (empty($this->commands)) {
            return $next($conversation);
        }
        $text = $conversation->getMessage()->getTrimText();

        if (!Str::startsWith($text, $this->commandMark)) {
            return $next($conversation);
        }

        $commandText = (string) substr($text, strlen($this->commandMark));
        $input = new StringInput($commandText);
        foreach ($this->commands as $command) {
            /**
             * @var AnalyzerCommand $command
             */
            if ($command->match($input)) {
                return $command->handle($input, $conversation);
            }
        }

        $returnr = $next($conversation);
        return $returnr;
    }

    public function getCommands() : array
    {
        return $this->commands;
    }


}