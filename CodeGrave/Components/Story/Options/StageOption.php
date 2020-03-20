<?php


namespace Commune\Components\Story\Options;
use Commune\Support\Option;


/**
 * 每一个 Episode 的结局.
 * 完成一个结局, 就会在用户的脚本记忆中增加一个已完成结局.
 *
 * @property-read string $id episode stage的ID.
 * @property-read string $title 结局的介绍.
 * @property-read string[] $stories 故事内容, 都是 replyId. 进入结局时会依次播放.
 * @property-read string[] $middleware 中间件, 类名, 传入Stage实例化, 执行__invoke, 返回Navigator的话会中断后续流程.
 * @property-read string[] $getItem 用户得到的道具. 会按键值对给道具赋值.
 * @property-read string $unlockEpisode 解锁的章节名称. 用户可以玩新的章节.
 * @property-read RedirectOption[] $redirects 可选, 会触发重定向逻辑.
 * @property-read ConfirmOption[] $confirms 可选, 会触发确认要求逻辑.
 * @property-read ChooseOption[] $choose 可选, 会触发让用户选择的逻辑.
 * @property-read string $isGoodEnding 是否是好结局. 默认不是.
 *
 * 以上逻辑都不触发的话, 会认为是一个 ending.
 */
class StageOption extends Option
{
    const IDENTITY = 'id';

    protected static $associations = [
        'redirects[]' => RedirectOption::class,
        'confirms[]' => ConfirmOption::class,
        'choose[]' => ChooseOption::class,
    ];

    public static function stub(): array
    {
        return [
            'id' => '',
            'title' => '',


            // 故事陈述的内容.
            'stories' => [
            ],
            // 进入时的事件.
            'middleware' => [
            ],
            // 可选. 获取道具事件.
            'getItem' => [
                //'item' => 'enum',
            ],
            // 可选. 解锁章节事件.
            'unlockEpisode' => '',
            // 可选. 重定向事件
            'redirects' => [
            ],
            // 可选. 确认事件.
            'confirms' => [
            ],
            'choose' => [
            ],
            'isGoodEnding' => 0,

        ];
    }


}