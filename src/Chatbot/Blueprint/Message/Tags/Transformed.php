<?php


namespace Commune\Chatbot\Blueprint\Message\Tags;


use Commune\Chatbot\Blueprint\Message\Message;

interface Transformed
{

    public function getOriginMessage() : Message;
}