<?php


namespace Commune\Components\SimpleWiki\Options;


use Commune\Support\Option;

/**
 * @property-read string $id  资源目录下第一个文件夹就是group ID
 * @property-read array $intentAlias 部分intent 的简写. 提高效率.
 * @property-read array $defaultSuggestions 默认的推荐内容.
 * @property-read string $question  猜你想问标准问题用语
 * @property-read string $askContinue  要用户继续时的标准用语
 * @property-read string $messagePrefix reply 消息的 id 的前缀.
 */
class GroupOption extends Option
{
    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'id' => 'demo',
            'intentAlias' => [
                // alias => intentName
            ],
            'defaultSuggestions' => [
                // default suggestions
            ],
            'question' => 'ask.needs',
            'askContinue' => 'ask.continue',
            'messagePrefix' => '',
        ];
    }


}