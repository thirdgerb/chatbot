<?php

namespace Commune\Demo;

use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\Actions\Talker;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;

class TestIntents extends OOContext
{
    const DESCRIPTION = '模拟命中意图';

    protected $suggestions = [
        '#testMessage',
        '#testNav',
        '#testTask',
        '#testAction'
    ];

    public function __hearing(Hearing $hearing) : void
    {
        $hearing
            // 输入 "m" 返回菜单
            ->is('m', Redirector::goStage('start'))
            // 输入 "q" 退出
            ->is('q', Redirector::goQuit());
    }

    public function __onStart(Stage $stage): Navigator
    {
        $menu = new Menu(
            '请选择您要进行的测试, 随时输入 m 返回菜单, 输入 q 退出.',
            [

                '模拟匹配单个意图' => 'isIntent',

                '模拟匹配一类意图' => 'isIntentIn',

                '模拟匹配任意意图' => 'isAnyIntent',

                '模拟运行单个意图' => 'runIntent',

                '模拟运行一类意图' => 'runIntentIn',

                '模拟运行任意意图' => 'runAnyIntent',
            ]
        );

        return $stage->component($menu);
    }

    public function __onIsIntent(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbal(
                '请测试匹配单个意图. 可用命令来模拟意图. ',
                $this->suggestions
            )
            ->hearing()
            ->isIntent('demo.lesions.message', [$this, 'matchIntent'])
            ->isIntent('demo.lesions.nav', [$this, 'matchIntent'])
            ->isIntent('demo.lesions.task', [$this, 'matchIntent'])
            ->isIntent('demo.lesions.action', [$this, 'matchIntent'])
            ->end();
    }

    public function __onIsIntentIn(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbal(
                '请测试匹配一类意图. ',
                $this->suggestions
            )
            ->hearing()
            // 使用意图名的前缀, 作为命名空间
            ->isIntentIn(['demo.lesions'], [$this, 'matchIntent'])
            ->end();
    }

    public function __onIsAnyIntent(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbal(
                '请测试匹配任意意图. PHP 的匹配规则会失效. 可以用 #intentName# 方式模拟',
                [
                    '#demo.lesions.message#',
                    '#demo.lesions.nav#',
                    '#demo.lesions.task#',
                    '#demo.lesions.action#',
                ]
            )
            ->hearing()
            ->isAnyIntent([$this, 'matchIntent'])
            ->end();
    }

    public function __onRunIntent(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbal(
                '测试运行单个意图. 请输入命令表示命中意图. 命中后会直接执行',
                $this->suggestions
            )
            ->hearing()
            ->runIntent('demo.lesions.message')
            ->runIntent('demo.lesions.nav')
            ->runIntent('demo.lesions.task')
            ->runIntent('demo.lesions.action')
            ->end(Talker::say()->info('似乎什么事都没发生'));
    }

    public function __onRunIntentIn(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbal(
                '测试运行一类意图. 请输入命令表示命中意图. 命中后会直接执行',
                $this->suggestions
            )
            ->hearing()
            // 使用意图的前缀作为命名空间
            ->runIntentIn(['demo.lesions'])
            ->end(Talker::say()->info('似乎什么事都没发生'));
    }

    public function __onRunAnyIntent(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbal(
                '测试运行一类意图. 请输入命令表示命中意图. 命中后会直接执行',
                $this->suggestions
            )
            ->hearing()
            ->runAnyIntent()
            ->end(Talker::say()->info('似乎什么事都没发生'));
    }

    public function matchIntent(IntentMessage $intent, Dialog $dialog) : Navigator
    {
        $dialog->say()->info(
            '命中意图名称: %name%; 简介: %desc%',
            [
                'name' => $intent->getName(),
                'desc' => $intent->getDef()->getDesc()
            ]
        );

        // 重复当前状态.
        return $dialog->repeat();
    }
}