<?php


namespace Commune\Chatbot\App\Components;

use Commune\Chatbot\App\Components\SimpleChat\LoadSimpleChat;
use Commune\Chatbot\Framework\Component\ComponentOption;


/**
 * 闲聊组件. 用最简单的方式来定义闲聊.
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

    }

    public static function stub(): array
    {
        return [
            'resourcePath' =>__DIR__ . '/SimpleChat/resources/',
        ];
    }


}