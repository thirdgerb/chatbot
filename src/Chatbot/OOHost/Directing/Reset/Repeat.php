<?php


namespace Commune\Chatbot\OOHost\Directing\Reset;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 重新启动当前stage. 重复所有的开启逻辑.
 */
class Repeat extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        return $this->startCurrent();
    }
}