<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Support\Option;

/**
 *
 * 命令的写法. 同时也会定义参数
 * 写法参考laravel 的 command
 *
 * '命令名 {参数1 : 简介}
 *      {可选参数2? : 简介}
 *      {--n|name : 选项介绍}
 * '
 * @property-read string $signature
 *
 *
 * 符合NLU需求的训练样本.
 *
 * 每个元素是一个样本. 用类似markdown link 的语法来标记entity.
 *
 * 例如 "这句话的Entity就在[句尾](where)"
 *
 * @property-read string[] $examples
 *
 *
 *
 * 每个元素可以是字符串或是数组
 * 如果是字符串, 则字符串之间是 "与" 的关系, 例如 ["必须", "要有", "关键字"]
 * 如果元素本身是数组, 则是 "或" 的关系, 例如 [ ["必须", "必要"], "要有", "关键字"]
 *
 * @property-read array $regex
 *
 *
 *
 * 每个元素可以是字符串或是数组
 * 如果是字符串, 则字符串之间是 "与" 的关系, 例如 ["必须", "要有", "关键字"]
 * 如果元素本身是数组, 则是 "或" 的关系, 例如 [ ["必须", "必要"], "要有", "关键字"]
 *
 * @property-read string[] $keywords
 *
 */
class IntentMatcherOption extends Option
{
    public static function stub(): array
    {
        return [
            'signature' => '', //'command {test1}',
            'examples' => [
                //'test [value](key)',
            ],
            'regex' => [
                //['/pattern1/', 'key1', 'key2', 'key3'],
                //['/pattern2/', 'key1', 'key2', 'key3'],
            ],
            'keywords' => [
                //'a' ,'b', ['synonym1', 'synonym2']
            ],
        ];
    }
}