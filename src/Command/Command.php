<?php

/**
 * Class AnalyzerCommand
 * @package Commune\Chatbot\Analyzer
 */

namespace Commune\Chatbot\Command;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Contracts\SessionDriver;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Directing\Director;
use Commune\Chatbot\Framework\Intent\IntentDefinition;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Text;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Framework\Session\Session;
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
     * @var ChatbotApp
     */
    protected $app;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var SessionDriver
     */
    protected $sessionDriver;

    public function __construct(
        ChatbotApp $app,
        SessionDriver $sessionDriver,
        Router $router,
        LoggerInterface $log
    )
    {
        $this->app = $app;
        $this->log = $log;
        $this->router = $router;
        $this->sessionDriver = $sessionDriver;
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

    abstract protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation;

    public function handle(MsgCmdIntent $intent, Conversation $conversation): Conversation
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

        return $this->handleIntent($intent, $conversation);
    }

    protected function getSession(Conversation $conversation)
    {
        return new Session(
            $this->app,
            $this->sessionDriver,
            $this->log,
            $this->router,
            $conversation
        );
    }

    protected function getDirector(Session $session)
    {
        return new Director(
            $this->app,
            $session,
            $this->router
        );

    }
}