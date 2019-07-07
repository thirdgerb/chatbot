<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 直接替换掉当前的thread
 * 需要明白为什么要这么做.
 */
class ReplaceThreadTo extends Redirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->replaceThreadTo($this->to);
        return $this->startCurrent();
    }
}