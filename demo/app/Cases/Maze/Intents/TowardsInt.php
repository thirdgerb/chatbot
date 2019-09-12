<?php


namespace Commune\Demo\App\Cases\Maze\Intents;


/**
 * @property-read string $toward 前进的方向
 */
class TowardsInt extends AbsMazeInt
{
    const SIGNATURE = 'towards {toward : 前进的方向}';
    const DESCRIPTION = '前进的方向';

    const CASTS = ['toward' => 'string'];

    public static function getContextName(): string
    {
        return static::CONTEXT_NAME_PREFIX . '.towards' ;
    }

}