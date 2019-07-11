<?php


namespace Commune\Chatbot\App\Components;

use Commune\Chatbot\App\Components\SimpleChat\LoadSimpleChat;
use Commune\Chatbot\Framework\Component\ComponentOption;


/**
 * 闲聊组件. 用最简单的方式来定义闲聊.
 * 在目录下定义多个文件, 每个文件里定义一套闲聊策略.
 *
 * 可以在 hearing 中 使用 SimpleChatAction, 来开启闲聊.
 *
 * $hearing->interceptor(new SimpleChatAction($domain),
 * $hearing->fallback(new SimpleChatAction($domain),
 *
 * $domain 和 resourcePath 下的文件名
 *
 * @property-read string[] $resourcePath
 */
class SimpleChatComponent extends ComponentOption
{
    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Chatbot\\App\\Components\\SimpleChat\\Tasks\\",
            __DIR__ .'/SimpleChat/Tasks'
        );

        $this->app->registerReactorService(
            new LoadSimpleChat(
                $this->app->getReactorContainer(),
                $this->resourcePath
            )
        );

        // 依赖
        $this->dependComponent(NLUExamplesComponent::class);
    }

    public static function stub(): array
    {
        return [
            'resourcePath' =>__DIR__ . '/SimpleChat/resources/',
        ];
    }


}