<?php


namespace Commune\Chatbot\OOHost\Directing\Dialog;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 等待用户发出信息.
 */
class Wait extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        return null;
    }
}