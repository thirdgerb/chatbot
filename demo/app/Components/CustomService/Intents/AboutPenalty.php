<?php


namespace Commune\Demo\App\Components\CustomService\Intents;


use Commune\Chatbot\App\Intents\ActionIntent;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class AboutPenalty extends ActionIntent
{

    const SIGNATURE = 'aboutPenalty';

    const KEYWORDS = [
        '违约金'
    ];

    const DESCRIPTION = '违约金介绍';


    public function action(Stage $stageRoute): Navigator
    {
        return $stageRoute->talk(function(Dialog $dialog){
            $dialog->say()
                ->askVerbose('您好, 目前租赁模式有3种', [
                   '随租随还', '经营租赁', '融资租赁'
                ]);
            $dialog->say('您看想了解哪一块?');
            return $dialog->wait();

        }, function(Dialog $dialog, Message $message){

        });
    }

    public function __exiting(Exiting $listener): void
    {
    }


}