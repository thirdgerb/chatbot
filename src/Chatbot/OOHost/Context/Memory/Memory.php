<?php


namespace Commune\Chatbot\OOHost\Context\Memory;

use Commune\Chatbot\OOHost\Context\Context;

interface Memory extends Context
{
    public function getScopingTypes() : array;

}