<?php


namespace Commune\Chatbot\App\Components\Configurable\Controllers;


use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Hearing;

abstract class Controller extends TaskDef
{

    public function __hearing(Hearing $hearing)
    {
        $hearing->component(new Operate());
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

}