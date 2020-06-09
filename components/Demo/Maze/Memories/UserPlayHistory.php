<?php

/**
 * Class UserPlayHistory
 * @package Commune\Components\Demo\Maze\Memories
 */

namespace Commune\Components\Demo\Maze\Memories;


use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;
use Commune\Ghost\Memory\AbsRecall;

/**
 * @property int $total
 * @property int $highestScore
 *
 * @desc 用户游戏历史信息
 */
class UserPlayHistory extends AbsRecall
{

    public static function __scopes(): array
    {
        return [ClonerScope::GUEST_ID];
    }

    public static function __attrs(): array
    {
        return [
            'total' => 0,
            'highestScore' => 0,
        ];
    }

}