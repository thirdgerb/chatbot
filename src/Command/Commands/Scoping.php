<?php

/**
 * Class History
 * @package Commune\Chatbot\Analyzer\Commands
 */

namespace Commune\Chatbot\Command\Commands;

use Commune\Chatbot\Command\Command;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Predefined\MsgCmdIntent;
use Commune\Chatbot\Framework\Message\Text;


class Scoping extends Command
{
    protected $signature = 'scope';

    protected $description = '';



    protected function handleIntent(MsgCmdIntent $intent,\Closure $next,  Conversation $conversation): Conversation
    {
        $scope = $conversation->getScope();
        $json = json_encode($scope->toMap(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $conversation->reply(new Text($json));
        return $conversation;
    }

}