<?php


namespace Commune\Chatbot\OOHost\Directing;

interface Navigator
{
    /**
     * @return Navigator|null
     * @throws \Commune\Chatbot\OOHost\Exceptions\NavigatorException
     */
    public function display() : ? Navigator;
}