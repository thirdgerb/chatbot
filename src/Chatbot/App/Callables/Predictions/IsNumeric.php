<?php


namespace Commune\Chatbot\App\Callables\Predictions;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\OOHost\Context\Callables\Prediction;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;

class IsNumeric implements Prediction
{
    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ): bool
    {
        return $message instanceof VerbalMsg
            && is_numeric($message->getTrimmedText());
    }


}