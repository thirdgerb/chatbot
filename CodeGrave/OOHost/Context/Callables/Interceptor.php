<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 通常用于进入一个对话, 机器人主动发言时的拦截.
 */
interface Interceptor
{
    public function __invoke(
        Context $self,
        Dialog $dialog
    ) : ? Navigator;

}