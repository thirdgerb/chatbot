# CommuneChatbot

> 开源的工程化对话机器人框架, 目的是用编程的方式实现复杂多轮对话管理, 用于开发跨平台的对话机器人或语音应用.

"Commune" 是 __亲切交谈__ 的意思. CommuneChatbot 这个项目是想通过 "对话" 的形式提供一种人与机器的交互方式. 在这个项目的思路中, "对话" 并非目的, 而是 "操作机器" 的手段.

简单来说, CommuneChatbot 是一个 :

- 使用 PHP7 开发的开源项目
- 基于 swoole + hyperf 提供协程化的服务端
- 可对接语音, 即时通讯等平台搭建对话机器人
- 最大特点是 __多轮对话管理引擎__, 用于解决 [复杂多轮对话问题](/zh-cn/core-concepts/complex-conversation.md)
- 提供工程化 (模块化/可配置/组件化) 的开发框架
- 目标是以对话的交互形式, 开发出像网站, 触屏App一样复杂的应用

如有兴趣, 可以加入讨论 QQ 群: 907985715

## Demo

目前的 Demo 有:

* 官方网站 : https://communechatbot.com/
* 开发文档 : https://communechatbot.com/docs/zh-cn/
* [项目网站](https://communechatbot.com)
* 微信公众号 Demo: 搜索 "CommuneChatbot"
* 百度智能音箱: 对音箱说 "打开三国群英传", "打开方向迷宫"

## 项目构成

- [Chatbot](https://github.com/thirdgerb/chatbot) : 机器人核心框架
- [Studio](https://github.com/thirdgerb/studio-hyperf) : 工作站, 基于 [Swoole](https://github.com/swoole/swoole-src) + [Hyperf](https://github.com/hyperf/hyperf) 开发, 可创建和运行应用
- [Chatbot-book](https://github.com/thirdgerb/chatbot-book) : 机器人开发手册项目

推荐使用 [Studio](https://github.com/thirdgerb/studio-hyperf) 搭建应用.
而 [Chatbot](https://github.com/thirdgerb/chatbot) 也可以嵌入到其它应用级框架中,
提供对话机器人服务.

更多细节请查看[开发手册](https://communechatbot.com/docs/zh-cn/), 或查看[开发手册的源码](https://github.com/thirdgerb/chatbot-book).

## 快速启动

安装项目:

    # 安装项目
    git clone https://github.com/thirdgerb/chatbot.git
    cd chatbot/

    # composer 安装依赖
    composer install

确认依赖:

- php >= 7.2
- php 基础扩展
- php 扩展 [intl](https://www.php.net/manual/en/book.intl.php) 用于国际化

运行命令行 demo :

    # 命令行demo, 基于 clue/stdio-react 项目
    php demo/console.php

    # tcp demo, 基于 swoole. 可以用 telnet 127.0.0.1 9501 连接.
    php demo/swoole.php

用 CommuneChatbot 开发多轮对话, 一个简单的示例如下 :

```php
/**
 * Context for hello world
 *
 * @property string $name userName
 */
class HelloWorldContext extends OOContext
{
    const DESCRIPTION = 'hello world!';

    public function __onStart(Stage $stage) : Navigator
    {
        return $stage->buildTalk()

            // send message to user
            ->info('hello world!!')

            // ask user name
            ->goStage('askName')
    }

    public function __onAskName(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            // ask user for name
            ->askVerbal('How may I address you?')

            // wait for user message
            ->hearing()

            // message is answer
            ->isAnswer(function(Answer $answer, Dialog $dialog) {

                // set Context memory
                $this->name = $answer->toResult();

                // go to "menu" stage
                return $this->goStage('menu');
            })

            // finish building Hearing AIP
            ->end();
    }

    public function __onMenu(Stage $stage) : Navigator
    {
        // build menu component
        $menu = new Menu(
            // menu question
            'What can I help you?',

            // answer suggesions
            [

                // go to play game context
                PlayGameContext::class,

                // go to order drinks
                OrderDrinkContext::class,

                // go to simple chat
                SimpleChatContext::class,
            ]
        );

        return $stage

            // after target context fulfilled
            ->onFallback(function(Dialog $dialog) {
                // repeat current menu stage
                return $dialog->repeat();
            });

            // use component
            ->component($menu);
    }
}
```



