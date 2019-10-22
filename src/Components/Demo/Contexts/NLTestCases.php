<?php


namespace Commune\Components\Demo\Contexts;

use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\App\Messages\Replies\Link;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Demo\Cases\Drink\OrderJuiceInt;
use Commune\Components\Demo\Cases\Maze\MazeInt;
use Commune\Components\Demo\Cases\Weather\TellWeatherInt;

/**
 * 自然语言用例.
 */
class NLTestCases extends TaskDef
{
    const DESCRIPTION = 'demo.contexts.nlCases';

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('demo.dialog.nlTest')
            ->goStage('menu');

    }

    public function __onMenu(Stage $stage) : Navigator
    {
        $menu = new Menu(
            'ask.needs',
            [
                'story.examples.sanguo.changbanpo',
                MazeInt::class,
                OrderJuiceInt::class,
                '查询天气用例' => [$this, 'weather'],
                '闲聊测试' => [$this, 'simpleChat'],
                'b :: 返回' => Redirector::goFulfill(),
            ]
        );

        $menu->onHearing(function(Hearing $hearing) {
            $hearing
                ->runIntent(MazeInt::class)
                ->runIntent(TellWeatherInt::class)
                ->runAnyIntent();
        });

        return $stage
            ->onFallback(function(Dialog $dialog){
                $dialog->say()->info("完成测试");
                return $dialog->repeat();
            })
            ->component($menu);
    }

    public function weather(Dialog $dialog) : Navigator
    {
        $dialog->say()->info('本 Demo 实现了一个简单的天气查询用例. 试着说 "北京今天的天气如何"类似的话, 也可以用命令行 "#tellWeather# 的方式触发.');

        return $dialog->repeat();
    }

    public function simpleChat(Dialog $dialog) : Navigator
    {
        $dialog->say()
            ->info(<<<EOF
由于本项目的重点在于复杂多轮对话, 对于闲聊目前只做了非常简单的实现.
一切闲聊内容基于 "闲聊组件" 的配置, 随机生成回复.
EOF
            )
            ->withReply(new Link(
                'https://github.com/thirdgerb/chatbot/blob/master/src/Components/SimpleChat/resources/example.yml',
                '打开链接可查看配置内容'
            ))
            ->info("您可以随时说以下内容:
- 你好
- 讲笑话
- 如何联系你
- 你傻吗

试试自然语言匹配的效果
");
        return $dialog->repeat();
    }

}