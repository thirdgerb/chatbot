<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\OOHost\Context\Hearing;

interface HearingComponent
{
    public function __invoke(Hearing $hearing) : void;
}