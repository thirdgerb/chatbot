<?php


namespace Commune\Chatbot\App\Intents;


use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 有预定义行动(action) 的intent.
 * 如果不被hearing 拦截, 会进入自己的context, 并执行对话逻辑.
 */
abstract class ActionIntent extends AbsCmdIntent
{
    // 意图的描述
    const DESCRIPTION = 'should define description';

    // 命令名. 可以用命令的方式来匹配
    const SIGNATURE = '';
    // 用正则来匹配
    const REGEX = [];
    // 用关键字来匹配.
    const KEYWORDS = [];

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $this->action($stage);
    }

    abstract public function action(Stage $stageRoute): Navigator;

}