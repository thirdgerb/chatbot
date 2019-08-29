<?php


namespace Commune\Chatbot\App\Components\Configurable\Controllers;


use Commune\Chatbot\OOHost\Context\Callables\HearingComponent;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeechImpl;

class Operate implements HearingComponent
{
    const OPERATIONS = ['quit', 'fulfill', 'cancel', 'back', 'reset'];

    public function __invoke(Hearing $hearing): void
    {
        $hearing->is('quit', function(Dialog $dialog) {
            $dialog->say()->info('do quit');
            return $dialog->quit();

        })->is('fulfill', function(Dialog $dialog){
            return $dialog->fulfill();

        })->is('cancel', function(Dialog $dialog) {
            $dialog->say()->info('do fulfill');
            return $dialog->cancel();

        })->is('back', function(Dialog $dialog) {
            $dialog->say()->info('do backward');
            return $dialog->backward();

        })->is('reset', function(Dialog $dialog) {
            $dialog->say()->info('do reset');
            return $dialog->redirect->replaceTo(EntryIntent::class);

        });
    }


}