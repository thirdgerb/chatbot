<?php


namespace Commune\Components\UnheardLike\Options;


use Commune\Support\Option;

/**
 * @property-read string $query 问题
 * @property-read string[] $choices 选项.
 * @property-read string[] $replies 每个选项选择后的回复
 * @property-read string $answer 正确答案
 */
class GameQuestion extends Option
{
    public static function stub(): array
    {
        return [
            'query' => '',
            'choices' => [
            ],
            'replies' => [
            ],
            'answer' => '',
        ];
    }


}