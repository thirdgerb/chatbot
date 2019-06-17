<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

interface Interceptor
{
    public function __invoke(
        Context $self,
        Dialog $dialog
    ) : ? Navigator;

}