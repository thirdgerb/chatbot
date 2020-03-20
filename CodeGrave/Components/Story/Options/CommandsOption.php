<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * 脚本中可用的命令形式.
 *
 * @property-read string $menu
 * @property-read string $returnGame
 * @property-read string $chooseEpisode
 * @property-read string $help
 * @property-read string $unlockEndings
 * @property-read string $quit
 *
 * @property-read string $skip
 * @property-read string $backward
 * @property-read string $restart
 * @property-read string $repeat
 *
 */
class CommandsOption extends Option
{
    public static function stub(): array
    {
        return [
            'menu' => '打开菜单',
            'returnGame' => '继续游戏',
            'chooseEpisode' => '选择章节',
            'help' => '操作简介',
            'unlockEndings' => '解锁结局',
            'quit' => '退出游戏',

            'skip' => '跳过',
            'backward' => '回到上一步',
            'restart' => '重新开始',
            'repeat' => '重复内容',
        ];
    }


}