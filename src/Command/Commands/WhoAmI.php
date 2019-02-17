<?php

/**
 * Class WhoAmI
 * @package Commune\Chatbot\Analyzer\Commands
 */

namespace Commune\Chatbot\Command\Commands;


use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Text;

class WhoAmI extends Command
{
    protected $signature = 'whoami';

    protected $description = 'test';


    protected function handleIntent(MsgCmdIntent $intent, Conversation $conversation): Conversation
    {
        $user = $conversation->getSender();
        $conversation->reply(new Text($user->toJson()));
        return $conversation;
    }

}