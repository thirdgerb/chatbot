<?php


namespace Commune\Chatbot\App\Components;

use Commune\Chatbot\App\Components\SimpleChat\Callables\SimpleChatAction;
use Commune\Chatbot\App\Components\SimpleChat\LoadSimpleChat;
use Commune\Chatbot\App\Components\SimpleChat\SimpleChatOption;
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
 * @property-read string $default
 * @property-read SimpleChatOption[] $resources
 */
class SimpleChatComponent extends ComponentOption
{

    protected static $associations = [
        'resources[]' => SimpleChatOption::class,
    ];

    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Chatbot\\App\\Components\\SimpleChat\\Tasks\\",
            __DIR__ .'/SimpleChat/Tasks'
        );

        foreach ($this->resources as $option) {
            $this->app->registerProcessService(
                new LoadSimpleChat(
                    $this->app->getProcessContainer(),
                    $option
                )
            );
        }

        // 依赖
        $this->dependComponent(NLUExamplesComponent::class);

        // todo 不规范的做法.
        $this->app->getConversationContainer()
            ->singleton(SimpleChatAction::class);
    }

    public static function stub(): array
    {
        return [
            'default' => 'example',
            'resources' => [
                SimpleChatOption::stub(),
            ],
        ];
    }


}