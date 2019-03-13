<?php

/**
 * Class AnalyzerCommand
 * @package Commune\Chatbot\Analyzer
 */

namespace Commune\Chatbot\Command;

use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\HostDriver;
use Commune\Chatbot\Framework\Intent\IntentDefinition;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Text;
use Illuminate\Console\Parser;
use Psr\Log\LoggerInterface;

/**
 * 注意, 需要是无状态的.
 *
 * Class Command
 * @package Commune\Chatbot\Command
 */
abstract class Command
{
    const SINGLETON = true;

    protected $signature = '';

    protected $description = '';

    protected $name;

    protected $definition;

    protected $options = [];

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var HostDriver
     */
    protected $hostDriver;

    public function __construct(
        HostDriver $driver,
        LoggerInterface $log
    )
    {
        $this->log = $log;
        $this->hostDriver = $driver;
        $this->definition = new IntentDefinition();
        [$name, $arguments, $options] = Parser::parse($this->signature);
        $this->name = $name;
        $this->definition->addArguments($arguments);
        $this->definition->addOptions($options);
    }

    public function match(MsgCmdIntent $input) : bool
    {
        return $this->name === $input->getCommandName();
    }

    abstract protected function handleIntent(MsgCmdIntent $intent, \Closure $next, Conversation $conversation): Conversation;

    public function handle(MsgCmdIntent $intent, \Closure $next, Conversation $conversation): Conversation
    {
        $intent->bind($this->definition);
        $errors = $intent->getErrors();
        if (!empty($errors)) {
            $message = "command not valid : \n";
            foreach ($errors as $name => $value) {
                $message .= "$name : $value \n";
            }
            $conversation->reply(new Text($message));
            return $conversation;
        }

        return $this->handleIntent($intent, $next, $conversation);
    }

    public function resetName(string $name)
    {
        $this->name = $name;
    }

}