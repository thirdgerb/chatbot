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


class History extends Command
{
    protected $signature = 'history';

    protected $description = '';

    protected function handleIntent(MsgCmdIntent $intent,\Closure $next,  Conversation $conversation): Conversation
    {
        $session = $this->hostDriver->getSession($conversation);

        $history = $session->getHistory();

        $str = "before:\n";
        foreach($history->getBefore() as $before) {
            $str .= "$before\n";
        }

        $str .= "current:\n";
        $str .= '** '. $history->getCurrent() . "\n";
        $str .= "after\n";
        foreach ($history->getAfter() as $after) {
            $str.= "$after\n";
        }

        $conversation->reply(new Text($str));
        return $conversation;
    }

}