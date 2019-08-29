<?php


namespace Commune\Chatbot\Blueprint\Message\Tags;


use Commune\Chatbot\Blueprint\Message\Message;

/**
 * message transformed from origin message
 */
interface Transformed
{
    public function getOriginMessage() : Message;
}