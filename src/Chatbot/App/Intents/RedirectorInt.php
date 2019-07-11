<?php


namespace Commune\Chatbot\App\Intents;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;


/**
 * 重定向意图.作用就是重定向到另一个intent.
 * 同时又使用 Intent 的基本功能.
 */
abstract class RedirectorInt extends AbsCmdIntent
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


    /**
     * @return Context|string
     */
    abstract public function redirectTo();

    /**
     * 默认的跳转测试还是 sleepTo
     *
     * @param Dialog $dialog
     * @return Navigator|null
     */
    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        // 启动就重定向.
        return $stage
            ->buildTalk()
            ->replaceTo($this->redirectTo(), Redirect::NODE_LEVEL);
    }

    public function __exiting(Exiting $listener): void
    {
    }


}