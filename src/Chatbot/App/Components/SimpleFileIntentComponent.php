<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\App\Components\SimpleFileIntent\LoadSimpleFileIntent;
use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * 用简单的 markdown 文件来生成意图.
 * 用 md 文件的路径作为意图的名称.
 *
 * 基本逻辑是, 命中了意图后, 输出内容, 并提供猜你想问.
 * 相当于用意图做搜索的功能.
 * 是一种文档型知识库比较简洁的实现手段.
 *
 *
 * @property-read string $resourcePath
 *
 */
class SimpleFileIntentComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'resourcePath' => __DIR__ . '/SimpleFileIntent/',
        ];
    }

    protected function doBootstrap(): void
    {
        $this->app->registerReactorService(
            new LoadSimpleFileIntent(
                $this->app->getReactorContainer(),
                $this->resourcePath
            )
        );

    }



}