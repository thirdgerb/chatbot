<?php

/**
 * Class Quit
 * @package Commune\Chatbot\Analyzer\Commands
 */

namespace Commune\Chatbot\Command\Commands;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;

class Quit extends Command
{
    protected $signature = 'quit';

    protected $description = 'quit session';

    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $conversation->closeSession();
        return $conversation;
    }


}