<?php

namespace Commune\Demo\Intents;

use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 模拟命令型意图.
 *
 * @property string $entity1
 * @property string $entity2
 */
class ActionInt extends AbsCmdIntent
{
    // 定义命中意图的命令行输入
    // 可以用 "#testAction [entity1] [entity2]" 的方式模拟意图.
    const SIGNATURE = 'testAction 
    {entity1 : 请输入 Entity1 的值} 
    {entity2 : 请输入 Entity2 的值}';

    const DESCRIPTION = '模拟命令型意图';

    // 用注解定义 Entities
    public static function __depend(Depending $depending) : void
    {
        // 根据定义的命令, 自动生成 Entity
        $depending->onSignature(static::SIGNATURE);
    }

    public static function getContextName(): string
    {
        return 'demo.lesions.action';
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo($this);
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage
            ->onStart([$this, 'action'])
            ->buildTalk()
            ->fulfill();
    }

    public function action(Dialog $dialog) : void
    {
        $dialog->say()->info(
            '输入信息是 entity1:%entity1%, entity2:%entity2%. ',
            [
                'entity1' => $this->entity1,
                'entity2' => $this->entity2,
            ]
        );
    }
}