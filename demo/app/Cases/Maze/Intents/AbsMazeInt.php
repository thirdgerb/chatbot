<?php


namespace Commune\Demo\App\Cases\Maze\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

abstract class AbsMazeInt extends MessageIntent
{
    const CONTEXT_NAME_PREFIX  = 'commune.demo.maze';

    public static function getContextName(): string
    {
        return static::CONTEXT_NAME_PREFIX . '.' . static::SIGNATURE;
    }

}