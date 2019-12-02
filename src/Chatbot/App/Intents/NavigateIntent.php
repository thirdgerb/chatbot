<?php


namespace Commune\Chatbot\App\Intents;


use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 导航类Intent.
 * 核心是navigate方法, 可以预定义如何操作语境变化.
 */
abstract class NavigateIntent extends AbsCmdIntent
{
    /**
     * @var string 简介.
     */
    const DESCRIPTION = 'should define intent description by constant';


    // 命令名. 可以用命令的方式来匹配
    const SIGNATURE = '';
    // 用正则来匹配
    const REGEX = [];
    // 用关键字来匹配.
    const KEYWORDS = [];
    // 给NLU用的例句.
    const EXAMPLES = [];


    public function __onStart(Stage $stageRoute): Navigator
    {
        return $stageRoute->dialog->fulfill();
    }

    abstract public function navigate(Dialog $dialog): ? Navigator;

}