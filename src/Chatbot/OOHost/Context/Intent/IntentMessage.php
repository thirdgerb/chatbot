<?php


namespace Commune\Chatbot\OOHost\Context\Intent;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 对用户意图的描述. 也被定义为context
 */
interface IntentMessage extends Context
{

    /**
     * 当一个Intent 试图拦截当前的对话时, 会执行此方法.
     * 通常是 return $dialog->sleepTo($this);
     * 从而进入intent 自己的逻辑.
     *
     * @param Dialog $dialog
     * @return Navigator|null
     */
    public function navigate(Dialog $dialog) : ? Navigator;

}