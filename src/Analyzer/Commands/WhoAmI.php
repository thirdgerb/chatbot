<?php

/**
 * Class WhoAmI
 * @package Commune\Chatbot\Analyzer\Commands
 */

namespace Commune\Chatbot\Analyzer\Commands;


use Commune\Chatbot\Analyzer\AnalyzerCommand;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Message\Text;
use Symfony\Component\Console\Input\InputInterface;

class WhoAmI extends AnalyzerCommand
{
    protected $signature = 'whoami';

    protected $description = 'test';

    public function handle(InputInterface $input, Conversation $conversation): Conversation
    {
        $user = $conversation->getSender();
        $conversation->reply(new Text($user->toJson()));
        return $conversation;
    }

}