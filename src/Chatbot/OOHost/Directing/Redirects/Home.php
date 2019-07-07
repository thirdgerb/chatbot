<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 将整个会话还原到起点.
 */
class Home extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $this->history->home();
        return $this->startCurrent();
    }


}