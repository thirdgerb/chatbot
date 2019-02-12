<?php

/**
 * Class History
 * @package Commune\Chatbot\Analyzer\Commands
 */

namespace Commune\Chatbot\Analyzer\Commands;

use Commune\Chatbot\Analyzer\AnalyzerCommand;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Message\Text;
use Symfony\Component\Console\Input\InputInterface;


class Scope extends AnalyzerCommand
{
    protected $signature = 'scope';

    protected $description = '';


    public function handle(InputInterface $input, Conversation $conversation): Conversation
    {
        $scope = $conversation->getScope();
        $json = json_encode($scope->toMap(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $conversation->reply(new Text($json));
        return $conversation;
    }

}