<?php


namespace Commune\Demo\App\Intents;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class TestInt extends NavigateIntent
{
    const SIGNATURE = 'test:intent';

    public static function getContextName(): string
    {
        return 'demo.testIntent';
    }

    public function __exiting(Exiting $listener): void
    {
    }


    public function navigate(Dialog $dialog): ? Navigator
    {
        $dialog->say()->info('hit test:intent');
        return $dialog->rewind();
    }
}