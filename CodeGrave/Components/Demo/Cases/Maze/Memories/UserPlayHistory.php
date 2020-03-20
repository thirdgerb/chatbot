<?php

/**
 * Class UserPlayHistory
 * @package Commune\Components\Demo\Cases\Maze\Memories
 */

namespace Commune\Components\Demo\Cases\Maze\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property int $total
 * @property int $highestScore
 */
class UserPlayHistory extends MemoryDef
{
    const DESCRIPTION = '用户游戏历史信息';
    const SCOPE_TYPES = [Scope::USER_ID];

    protected function init(): array
    {
        return [
            'total' => 0,
            'highestScore' => 0,
        ];
    }
}