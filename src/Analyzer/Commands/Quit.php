<?php

/**
 * Class Quit
 * @package Commune\Chatbot\Analyzer\Commands
 */

namespace Commune\Chatbot\Analyzer\Commands;


use Commune\Chatbot\Analyzer\AnalyzerCommand;
use Commune\Chatbot\Contracts\ServerDriver;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Symfony\Component\Console\Input\InputInterface;

class Quit extends AnalyzerCommand
{
    protected $signature = 'quit';

    protected $description = 'quit';

    protected $driver;

    public function __construct(ServerDriver $driver)
    {
        $this->driver = $driver;
        parent::__construct();
    }

    public function handle(InputInterface $input, Conversation $conversation): Conversation
    {
        $conversation->closeSession();
        return $conversation;
    }


}