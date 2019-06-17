<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

class ReplaceThreadTo extends Redirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->replaceThreadTo($this->to);
        return $this->startCurrent();
    }
}