<?php


namespace Commune\Chatbot\App\Components;


use Commune\Chatbot\App\Components\SimpleFileChat\GroupOption;
use Commune\Chatbot\App\Components\SimpleFileChat\LoadSimpleFileIntent;
use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * 用简单的 markdown 文件来生成意图.
 * 用 md 文件的路径作为意图的名称.
 *
 * 基本逻辑是, 命中了意图后, 输出内容, 并提供猜您想问.
 * 相当于用意图做搜索的功能.
 * 是一种文档型知识库比较简洁的实现手段.
 *
 *
 * @property-read GroupOption[] $groups
 *
 */
class SimpleFileChatComponent extends ComponentOption
{
    protected static $associations = [
        'groups[]' => GroupOption::class,
    ];

    public static function stub(): array
    {
        return [
            'groups' => [
                GroupOption::stub(),
            ]
        ];
    }

    protected function doBootstrap(): void
    {
        foreach ($this->groups as $option) {
            $this->app->registerReactorService(
                new LoadSimpleFileIntent(
                    $this->app->getReactorContainer(),
                    $option
                )
            );
        }

    }



}