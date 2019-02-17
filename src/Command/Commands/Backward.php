<?php

/**
 * Class Back
 * @package Commune\Chatbot\Command\Commands
 */

namespace Commune\Chatbot\Command\Commands;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;

class Backward extends Command
{
    protected $signature = 'back';

    protected $description = 'backward to previous context';

    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $director = $this->getDirector($this->getSession($conversation));
        return $director->backward();
    }


}