<?php

namespace Commune\Demo\Intents;

use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class TaskInt extends AbsCmdIntent
{
    // 定义命令, 用 #testTask 方式可以模拟意图命中
    const SIGNATURE = 'testTask';

    const DESCRIPTION = '模拟任务类意图';

    // 定义意图的名称
    public static function getContextName(): string
    {
        return 'demo.lesions.task';
    }

    // 定义默认的全局事件
    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    // 定义任务的流程
    public function __onStart(Stage $stage): Navigator
    {
        // 假装执行了任务, 经过了 1, 2, 3 步
        return $stage->buildTalk()
            ->goStagePipes(['step1', 'step2', 'step3', 'final']);
    }

    public function __onStep1(Stage $stage) : Navigator
    {
        return $this->step($stage);
    }

    public function __onStep2(Stage $stage) : Navigator
    {
        return $this->step($stage);
    }

    public function __onStep3(Stage $stage) : Navigator
    {
        return $this->step($stage);
    }

    public function __onFinal(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info('模拟任务执行结束, 退出语境')
            ->fulfill();
    }

    public function step(Stage $stage) : Navigator
    {
        $name = $stage->name;
        return $stage->buildTalk()
            ->info("进入 stage : $name")
            ->info("输入任何信息进入下一步")
            ->wait()
            ->next();
    }
}