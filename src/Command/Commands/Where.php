<?php

/**
 * Class Where
 * @package Commune\Chatbot\Command\Commands
 */

namespace Commune\Chatbot\Command\Commands;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Text;

class Where extends Command
{
    protected $signature = 'where';

    protected $description = 'show where i am';

    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $context = $this->hostDriver->getDirector($this->hostDriver->getSession($conversation))->fetchCurrentContext();
        $conversation->reply(new Text($context->getDescription()));
        return $conversation;
    }


}