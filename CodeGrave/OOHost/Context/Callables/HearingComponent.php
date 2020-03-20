<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\OOHost\Dialogue\Hearing;

interface HearingComponent
{
    public function __invoke(Hearing $hearing) : void;
}