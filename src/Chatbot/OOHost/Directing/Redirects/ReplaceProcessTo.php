<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

class ReplaceProcessTo extends Redirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->replaceProcessTo($this->to);
        return $this->startCurrent();
    }
}