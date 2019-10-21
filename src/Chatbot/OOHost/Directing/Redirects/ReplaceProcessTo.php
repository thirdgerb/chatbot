<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 直接替换掉整个process. 需要明白为什么要这么做.
 */
class ReplaceProcessTo extends AbsRedirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->replaceProcessTo($this->to);
        return $this->startCurrent();
    }
}