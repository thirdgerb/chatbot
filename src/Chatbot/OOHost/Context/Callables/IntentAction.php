<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;

interface IntentAction
{
    public function __invoke(
        Context $self,
        Dialog $dialog,
        IntentMessage $intent
    ) : Navigator;
}