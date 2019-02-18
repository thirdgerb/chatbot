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
use Commune\Chatbot\Framework\Support\ChatbotUtils;

class Locate extends Command
{

    protected $signature = 'location';

    protected $description = '';

    protected $app;


    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $session = $this->hostDriver->getSession($conversation);

        $location = $session->getHistory()->current();
        $conversation->reply(new Text(
            'at:'.$location->toJson(
                ChatbotUtils::JSON_OPTION | JSON_PRETTY_PRINT
            )
        ));
        return $conversation;
    }


}