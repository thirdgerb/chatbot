<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

class YieldTo extends Redirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->yieldTo($this->to);
        return $this->startCurrent();
    }


}