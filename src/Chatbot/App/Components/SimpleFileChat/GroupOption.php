<?php


namespace Commune\Chatbot\App\Components\SimpleFileChat;


use Commune\Support\Option;

/**
 * @property-read string $id
 * @property-read string $resourcePath
 * @property-read array $intentAlias 部分intent 的简写
 * @property-read array $defaultSuggestions 默认的推荐.
 * @property-read string $question  猜你想问标准问题用语
 * @property-read string $askContinue  要用户继续时的标准用语
 * @property-read string $skipMark  跳过连续对话的输入符号
 */
class GroupOption extends Option
{
    public static function stub(): array
    {
        return [
            'id' => 'test',
            'resourcePath' => __DIR__ ,
            'intentAlias' => [
                // alias => intentName
            ],
            'defaultSuggestions' => [
                // default suggestions
            ],
            'question' => 'ask.needs',
            'askContinue' => 'ask.continue',
            'skipMark' => '..',
        ];
    }


}