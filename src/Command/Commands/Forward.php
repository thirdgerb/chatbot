<?php

/**
 * Class Forward
 * @package Commune\Chatbot\Command\Commands
 */

namespace Commune\Chatbot\Command\Commands;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;

class Forward extends Command
{
    protected $signature = 'forward';

    protected $description = 'forward to last context';


    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $director = $this->getDirector($this->getSession($conversation));
        return $director->forward();
    }


}