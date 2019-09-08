<?php


namespace Commune\Chatbot\App\Intents;


use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 这种 Intent 主要作为消息来提供.
 * 不直接控制多轮对话.
 * 可以用注解等方式来定义参数.
 */
abstract class MessageIntent extends AbsCmdIntent
{
    const DESCRIPTION = 'should define description';


    // 命令名. 可以用命令的方式来匹配
    const SIGNATURE = '';
    // 用正则来匹配
    const REGEX = [];
    // 用关键字来匹配.
    const KEYWORDS = [];

    public function navigate(Dialog $dialog): ? Navigator
    {
        return null;
    }

    public function __onStart(Stage $stageRoute): Navigator
    {
        return $stageRoute->dialog->fulfill();
    }

    public static function __depend(Depending $depending): void
    {
        $depending->onAnnotations();
    }

    public function __exiting(Exiting $listener): void
    {
    }
}