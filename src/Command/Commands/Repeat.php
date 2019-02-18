<?php

/**
 * Class Repeat
 * @package Commune\Chatbot\Command\Commands
 */

namespace Commune\Chatbot\Command\Commands;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;

class Repeat extends Command
{
    protected $signature = 'repeat';

    protected $description = 'repeat current context';


    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $director = $this->hostDriver->getDirector($session = $this->hostDriver->getSession($conversation));
        return $director->repeat();
    }


}