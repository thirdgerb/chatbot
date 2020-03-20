<?php


namespace Commune\Chatbot\Blueprint\Message\QA;


interface Confirmation extends Answer
{
    public function isPositive() : bool;
}