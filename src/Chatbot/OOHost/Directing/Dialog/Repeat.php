<?php


namespace Commune\Chatbot\OOHost\Directing\Dialog;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Repeat extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        return $this->startCurrent();
    }
}