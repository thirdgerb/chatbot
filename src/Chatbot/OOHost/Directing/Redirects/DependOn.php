<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

class DependOn extends Redirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->dependOn($this->to);
        return $this->startCurrent();
    }


}