<?php


namespace Commune\Chatbot\App\Intents;


use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 可以当做context 来使用的intent
 */
abstract class ContextIntent extends AbsCmdIntent
{

    const DESCRIPTION = 'should define description';

    // 命令名. 可以用命令的方式来匹配
    const SIGNATURE = '';
    // 用正则来匹配
    const REGEX = [];
    // 用关键字来匹配.
    const KEYWORDS = [];
    // 给NLU用的例句.
    const EXAMPLES = [];

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }


}