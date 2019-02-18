<?php

/**
 * Class Where
 * @package Commune\Chatbot\Analyzer\Commands
 */

namespace Commune\Chatbot\Command\Commands;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Text;

class ShowContext extends Command
{
    protected $signature = 'context';

    protected $description = '';


    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $session = $this->hostDriver->getSession($conversation);

        $location = $session->getHistory()->current();
        $context = $session->fetchContextByLocation($location);

        $conversation->reply(new Text($context->toJson()));
        return $conversation;
    }


}