<?php


namespace Commune\Chatbot\OOHost\NLU;


use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Message\Message;

interface NatureLanguageUnit
{

    /**
     * @param Message $message
     * @return bool
     */
    public function messageCouldHandle(Message $message) : bool;

    /**
     * @param Message $message
     * @return MatchedIntent[]
     */
    public function matchIntents(Message $message) : array;

    /**
     * @param IncomingMessage $message
     */
    public function logUnmatchedMessage(IncomingMessage $message) : void;
}