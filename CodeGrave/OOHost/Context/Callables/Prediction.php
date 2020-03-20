<?php


namespace Commune\Chatbot\OOHost\Context\Callables;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;

/**
 * 通常用在hearing 的 expect 中, 判断一个条件是否存在.
 */
interface Prediction
{
    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ) : bool;


}